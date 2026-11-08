<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoicePoolRequest;
use App\Http\Requests\UpdateRevenueLimitRequest;
use App\Jobs\ProcessInvoiceXml;
use App\Models\InvoiceAuditLog;
use App\Models\InvoiceXml;
use App\Models\Setting;
use App\Services\DanfseService;
use App\Services\InvoicePoolService;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoicePoolController extends Controller
{
    public function __construct(
        private InvoicePoolService $pool,
        private StorageService $storage,
        private DanfseService $danfse,
    ) {}

    public function index(Request $request): View
    {
        $userId = Auth::id();

        $years = InvoiceXml::forUser($userId)
            ->whereNotNull('competence')
            ->selectRaw('distinct substr(competence, 1, 4) as year')
            ->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->push((int) now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        $year = (int) $request->input('year', now()->year);
        if (!$years->contains($year)) {
            $year = (int) now()->year;
        }

        $competence = $request->input('competence');
        if ($competence && !preg_match('/^' . $year . '-(0[1-9]|1[0-2])$/', $competence)) {
            $competence = null;
        }

        $queued = InvoiceXml::forUser($userId)->queued()->orderBy('created_at')->get();

        $notesQuery = InvoiceXml::forUser($userId)
            ->whereNotIn('status', [InvoiceXml::STATUS_PENDING, InvoiceXml::STATUS_PROCESSING])
            ->with(['invoice.billingGroup', 'company']);

        $competence
            ? $notesQuery->forCompetence($competence)
            : $notesQuery->where('competence', 'like', $year . '-%');

        $notesByMonth = $notesQuery
            ->orderByDesc('competence')
            ->orderByDesc('issued_at')
            ->get()
            ->groupBy('competence');

        // Notas com erro de leitura não têm competência: ficam num bloco próprio
        $withoutCompetence = InvoiceXml::forUser($userId)
            ->where('status', InvoiceXml::STATUS_ERROR)
            ->whereNull('competence')
            ->orderByDesc('created_at')
            ->get();

        $limit = Setting::forUser($userId)->annual_revenue_limit;
        $summary = $this->pool->annualSummary($userId, $year, $limit !== null ? (float) $limit : null);

        return view('invoices.pool', [
            'years'             => $years,
            'year'              => $year,
            'competence'        => $competence,
            'queued'            => $queued,
            'notesByMonth'      => $notesByMonth,
            'withoutCompetence' => $withoutCompetence,
            'summary'           => $summary,
            'storageQuota'      => $this->storage->getQuotaData(Auth::user()),
        ]);
    }

    public function store(StoreInvoicePoolRequest $request): JsonResponse
    {
        $user = Auth::user();
        $files = $request->file('xmls');

        $totalBytes = array_sum(array_map(fn ($file) => $file->getSize(), $files));
        if (!$this->storage->canUpload($user, $totalBytes)) {
            $quota = $this->storage->getQuotaData($user);
            return response()->json([
                'success' => false,
                'message' => "Cota de armazenamento insuficiente ({$quota['used_mb']} MB / {$quota['quota_mb']} MB). Exclua arquivos para liberar espaço.",
            ], 422);
        }

        $created = [];

        foreach ($files as $file) {
            $uuid = (string) Str::uuid();
            // Nome original não entra no path (evita sobrescrita e path traversal)
            $storagePath = 'invoices/' . $user->id . '/pool/' . $uuid . '.xml';

            Storage::put($storagePath, file_get_contents($file->getRealPath()));

            $xml = InvoiceXml::create([
                'uuid'          => $uuid,
                'user_id'       => $user->id,
                'status'        => InvoiceXml::STATUS_PENDING,
                'filename'      => Str::limit(basename($file->getClientOriginalName()), 250, ''),
                'path'          => $storagePath,
                'xml_file_size' => $file->getSize(),
            ]);

            $this->storage->add($user, $file->getSize());

            ProcessInvoiceXml::dispatch($xml);

            $created[] = $xml;
        }

        $count = count($created);

        return response()->json([
            'success' => true,
            'message' => $count === 1
                ? 'Nota enviada para processamento.'
                : "{$count} notas enviadas para processamento.",
            'xmls'    => collect($created)->map(fn ($xml) => $this->toStatusPayload($xml)),
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $uuids = array_slice((array) $request->input('uuids', []), 0, 200);

        $xmls = InvoiceXml::forUser(Auth::id())
            ->whereIn('uuid', $uuids)
            ->get();

        return response()->json([
            'xmls'    => $xmls->map(fn ($xml) => $this->toStatusPayload($xml)),
            'pending' => $xmls->filter->isQueued()->count(),
        ]);
    }

    public function retry(string $uuid): JsonResponse
    {
        $xml = InvoiceXml::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        if ($xml->status !== InvoiceXml::STATUS_ERROR) {
            return response()->json(['success' => false, 'message' => 'Apenas notas com erro podem ser reprocessadas.'], 422);
        }

        $xml->update(['status' => InvoiceXml::STATUS_PENDING, 'parse_error' => null]);

        ProcessInvoiceXml::dispatch($xml);

        return response()->json([
            'success' => true,
            'message' => 'Nota reenviada para processamento.',
            'xml'     => $this->toStatusPayload($xml),
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $xml = InvoiceXml::forUser(Auth::id())->where('uuid', $uuid)->with('invoice')->firstOrFail();

        if ($xml->invoice?->isClosed()) {
            return response()->json(['success' => false, 'message' => 'A nota está vinculada a uma fatura encerrada e não pode ser excluída.'], 403);
        }

        if ($xml->status === InvoiceXml::STATUS_PROCESSING) {
            return response()->json(['success' => false, 'message' => 'A nota está sendo processada. Aguarde alguns segundos.'], 422);
        }

        $freedBytes = (int) $xml->xml_file_size + (int) $xml->danfse_file_size;

        Storage::delete($xml->path);
        if ($xml->danfse_path) {
            Storage::delete($xml->danfse_path);
        }

        if ($xml->invoice) {
            InvoiceAuditLog::record($xml->invoice, 'xml_removido', 'XML removido via pool: ' . $xml->filename);
        }

        $xml->delete();

        $this->storage->remove(Auth::user(), $freedBytes);

        return response()->json(['success' => true, 'message' => 'Nota removida com sucesso.']);
    }

    public function download(string $uuid): StreamedResponse
    {
        $xml = InvoiceXml::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        abort_unless(Storage::exists($xml->path), 404);

        return Storage::download($xml->path, $xml->filename);
    }

    public function danfse(string $uuid): Response
    {
        $xml = InvoiceXml::forUser(Auth::id())->where('uuid', $uuid)->with('company')->firstOrFail();

        abort_unless($xml->supportsDanfse() && Storage::exists($xml->path), 404);

        try {
            $pdf = $this->danfse->render($xml);
        } catch (\Throwable $e) {
            report($e);
            abort(422, 'Não foi possível gerar o DANFSe desta nota.');
        }

        $filename = 'DANFSe-' . ($xml->invoice_number ?? $xml->access_key) . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function updateLimit(UpdateRevenueLimitRequest $request): JsonResponse
    {
        $settings = Setting::forUser(Auth::id());
        $settings->update(['annual_revenue_limit' => $request->validated('annual_revenue_limit')]);

        return response()->json(['success' => true, 'message' => 'Limite anual atualizado com sucesso.']);
    }

    private function toStatusPayload(InvoiceXml $xml): array
    {
        return [
            'uuid'         => $xml->uuid,
            'filename'     => $xml->filename,
            'status'       => $xml->status,
            'status_label' => $xml->statusLabel(),
            'error'        => $xml->parse_error,
        ];
    }
}
