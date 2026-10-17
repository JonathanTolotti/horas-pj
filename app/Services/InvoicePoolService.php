<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceAuditLog;
use App\Models\InvoiceXml;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InvoicePoolService
{
    public function __construct(private XmlParserService $parser) {}

    /**
     * Processa uma nota do pool: extrai dados do XML e vincula à fatura da competência
     * (criando a fatura se necessário). Erros de negócio ficam registrados na própria nota.
     */
    public function process(InvoiceXml $xml): void
    {
        $xml->update([
            'status'      => InvoiceXml::STATUS_PROCESSING,
            'attempts'    => $xml->attempts + 1,
            'parse_error' => null,
        ]);

        $parsed = $this->parser->parse($xml->path);

        if (!$parsed['xml_parsed']) {
            $this->fail($xml, $parsed['parse_error'] ?? 'Não foi possível ler o XML.', ['xml_parsed' => false]);
            return;
        }

        if (empty($parsed['competence'])) {
            $this->fail($xml, 'Competência não encontrada no XML.', ['xml_parsed' => false]);
            return;
        }

        // Nota duplicada não grava os dados, para não somar duas vezes no faturamento
        if ($parsed['access_key'] && InvoiceXml::forUser($xml->user_id)
                ->where('access_key', $parsed['access_key'])
                ->where('id', '!=', $xml->id)
                ->exists()) {
            $this->fail($xml, 'Nota já importada anteriormente (chave ' . $parsed['access_key'] . ').', [
                'xml_parsed'     => false,
                'invoice_number' => $parsed['invoice_number'],
            ]);
            return;
        }

        $data = collect($parsed)->except(['xml_parsed', 'parse_error'])->all();
        $data['xml_parsed'] = true;

        $company = $parsed['recipient_cnpj']
            ? Company::forUser($xml->user_id)->with('billingGroup')->where('cnpj', $parsed['recipient_cnpj'])->first()
            : null;

        if (!$company) {
            $this->fail($xml, 'Tomador ' . ($parsed['recipient_cnpj'] ?? 'sem CNPJ') . ' não está cadastrado em Empresas.', $data);
            return;
        }

        $data['company_id'] = $company->id;
        $group = $company->billingGroup;
        $lockKey = 'invoice-pool:' . $xml->user_id . ':' . ($group ? 'g' . $group->id : 'c' . $company->id) . ':' . $parsed['competence'];

        Cache::lock($lockKey, 30)->block(15, function () use ($xml, $data, $company, $group, $parsed) {
            DB::transaction(function () use ($xml, $data, $company, $group, $parsed) {
                $invoice = $this->findInvoice($xml->user_id, $parsed['competence'], $company, $group);

                if ($invoice && $invoice->isClosed()) {
                    $label = $invoice->status === Invoice::STATUS_CANCELLED ? 'cancelada' : 'encerrada';
                    $this->fail($xml, 'A fatura de ' . $this->formatCompetence($parsed['competence']) . " está {$label}. Reabra a fatura e reprocesse a nota.", $data);
                    return;
                }

                $created = false;
                if (!$invoice) {
                    $invoice = Invoice::create([
                        'user_id'          => $xml->user_id,
                        'company_id'       => $group?->primary_company_id ?? $company->id,
                        'billing_group_id' => $group?->id,
                        'title'            => 'Fatura ' . ($group?->name ?? $company->name) . ' - ' . $this->formatCompetence($parsed['competence']),
                        'reference_month'  => $parsed['competence'],
                        'status'           => Invoice::STATUS_DRAFT,
                    ]);
                    $created = true;
                }

                $xml->update(array_merge($data, [
                    'invoice_id'   => $invoice->id,
                    'status'       => InvoiceXml::STATUS_PROCESSED,
                    'processed_at' => now(),
                ]));

                if ($created) {
                    InvoiceAuditLog::record($invoice, 'fatura_criada', 'Fatura criada automaticamente pelo pool de notas.');
                }

                InvoiceAuditLog::record(
                    $invoice,
                    'xml_importado',
                    'XML importado via pool: ' . $xml->filename,
                    ['filename' => $xml->filename, 'xml_parsed' => true, 'access_key' => $parsed['access_key']],
                );
            });
        });
    }

    /**
     * Faturamento bruto do ano por competência, comparado ao limite configurado.
     */
    public function annualSummary(int $userId, int $year, ?float $limit): array
    {
        $rows = InvoiceXml::forUser($userId)
            ->where('xml_parsed', true)
            ->where('competence', 'like', $year . '-%')
            ->selectRaw('competence, count(*) as notes, sum(coalesce(gross_amount, amount, 0)) as gross, sum(coalesce(amount, 0)) as net')
            ->groupBy('competence')
            ->get()
            ->keyBy('competence');

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = sprintf('%04d-%02d', $year, $m);
            $row = $rows->get($key);
            $months[] = [
                'competence' => $key,
                'label'      => ucfirst(Carbon::create($year, $m, 1)->locale('pt_BR')->translatedFormat('M')),
                'notes'      => (int) ($row->notes ?? 0),
                'gross'      => round((float) ($row->gross ?? 0), 2),
                'net'        => round((float) ($row->net ?? 0), 2),
            ];
        }

        $gross = round(array_sum(array_column($months, 'gross')), 2);
        $net   = round(array_sum(array_column($months, 'net')), 2);

        // Projeção: média dos meses decorridos do ano × 12 (ano corrente); anos fechados = realizado
        $now = now();
        $elapsed = $year < $now->year ? 12 : ($year === $now->year ? $now->month : 0);
        $projection = $elapsed > 0 ? round($gross / $elapsed * 12, 2) : 0.0;

        $percentage = $limit > 0 ? round($gross / $limit * 100, 1) : null;

        return [
            'year'       => $year,
            'months'     => $months,
            'gross'      => $gross,
            'net'        => $net,
            'notes'      => array_sum(array_column($months, 'notes')),
            'limit'      => $limit,
            'remaining'  => $limit > 0 ? round($limit - $gross, 2) : null,
            'percentage' => $percentage,
            'projection' => $projection,
            'projection_percentage' => $limit > 0 ? round($projection / $limit * 100, 1) : null,
            'level'      => $percentage === null ? 'none' : ($percentage >= 100 ? 'over' : ($percentage >= 80 ? 'warning' : 'ok')),
        ];
    }

    private function findInvoice(int $userId, string $competence, Company $company, $group): ?Invoice
    {
        $query = Invoice::forUser($userId)->forMonth($competence);

        if ($group) {
            $companyIds = $group->companies()->pluck('id');
            $query->where(function ($q) use ($group, $companyIds) {
                $q->where('billing_group_id', $group->id)->orWhereIn('company_id', $companyIds);
            });
        } else {
            $query->where('company_id', $company->id);
        }

        // Prioriza faturas abertas; entre elas, a mais recente
        return $query
            ->orderByRaw("case when status in ('encerrada', 'cancelada') then 1 else 0 end")
            ->orderByDesc('created_at')
            ->first();
    }

    private function fail(InvoiceXml $xml, string $message, array $data = []): void
    {
        $xml->update(array_merge($data, [
            'status'       => InvoiceXml::STATUS_ERROR,
            'parse_error'  => $message,
            'invoice_id'   => null,
            'processed_at' => now(),
        ]));
    }

    private function formatCompetence(string $competence): string
    {
        [$y, $m] = explode('-', $competence);
        return "{$m}/{$y}";
    }
}
