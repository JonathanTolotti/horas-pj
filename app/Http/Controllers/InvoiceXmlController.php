<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceXmlRequest;
use App\Models\Invoice;
use App\Models\InvoiceAuditLog;
use App\Models\InvoiceXml;
use App\Services\StorageService;
use App\Services\XmlParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceXmlController extends Controller
{
    public function __construct(
        private XmlParserService $parser,
        private StorageService $storage,
    ) {}

    public function store(StoreInvoiceXmlRequest $request, string $invoiceUuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $user = Auth::user();
        $results = [];
        $allParsed = true;

        foreach ($request->file('xmls') as $file) {
            if (!$this->storage->canUpload($user, $file->getSize())) {
                $quota = $this->storage->getQuotaData($user);
                return response()->json([
                    'success' => false,
                    'message' => "Cota de armazenamento atingida ({$quota['used_mb']} MB / {$quota['quota_mb']} MB). Exclua arquivos para liberar espaço.",
                ], 422);
            }

            $storagePath = 'invoices/' . Auth::id() . '/' . $invoice->id . '/' . $file->getClientOriginalName();
            $fileSize    = $file->getSize();

            Storage::put($storagePath, file_get_contents($file->getRealPath()));

            $parsed = $this->parser->parse($storagePath);

            $invoiceXml = $invoice->xmls()->create([
                'filename'       => $file->getClientOriginalName(),
                'path'           => $storagePath,
                'xml_file_size'  => $fileSize,
                'invoice_number' => $parsed['invoice_number'],
                'amount'         => $parsed['amount'],
                'issued_at'      => $parsed['issued_at'],
                'provider_cnpj'  => $parsed['provider_cnpj'],
                'recipient_cnpj' => $parsed['recipient_cnpj'],
                'provider_name'  => $parsed['provider_name'],
                'recipient_name' => $parsed['recipient_name'],
                'xml_parsed'     => $parsed['xml_parsed'],
                'parse_error'    => $parsed['parse_error'],
            ]);

            $this->storage->add($user, $fileSize);

            if (!$parsed['xml_parsed']) {
                $allParsed = false;
            }

            InvoiceAuditLog::record(
                $invoice,
                'xml_importado',
                'XML importado: ' . $file->getClientOriginalName(),
                ['filename' => $file->getClientOriginalName(), 'xml_parsed' => $parsed['xml_parsed']],
            );

            $results[] = $invoiceXml;
        }

        $count = count($results);
        $message = $count === 1
            ? ($allParsed ? 'XML importado e processado com sucesso.' : 'XML importado. Alguns dados precisam ser preenchidos manualmente.')
            : ($allParsed ? "{$count} XMLs importados e processados com sucesso." : "{$count} XMLs importados. Alguns dados precisam ser preenchidos manualmente.");

        $fresh = $invoice->fresh();

        return response()->json([
            'success'        => true,
            'message'        => $message,
            'xmls'           => $results,
            'xml_total'      => $fresh->getXmlTotal(),
            'reconciliation' => $fresh->getReconciliationStatus(),
        ]);
    }

    public function destroy(string $invoiceUuid, string $xmlUuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $xml = InvoiceXml::where('invoice_id', $invoice->id)->where('uuid', $xmlUuid)->firstOrFail();

        $freedBytes = (int) $xml->xml_file_size + (int) $xml->danfse_file_size;

        Storage::delete($xml->path);
        if ($xml->danfse_path) {
            Storage::delete($xml->danfse_path);
        }

        InvoiceAuditLog::record($invoice, 'xml_removido', 'XML removido: ' . $xml->filename);

        $xml->delete();

        $this->storage->remove(Auth::user(), $freedBytes);

        return response()->json([
            'success'        => true,
            'message'        => 'XML removido com sucesso.',
            'xml_total'      => $invoice->fresh()->getXmlTotal(),
            'reconciliation' => $invoice->fresh()->getReconciliationStatus(),
        ]);
    }

    public function download(string $invoiceUuid, string $xmlUuid): StreamedResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        $xml = InvoiceXml::where('invoice_id', $invoice->id)->where('uuid', $xmlUuid)->firstOrFail();

        return Storage::download($xml->path, $xml->filename);
    }

    public function uploadDanfse(Request $request, string $invoiceUuid, string $xmlUuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $xml = InvoiceXml::where('invoice_id', $invoice->id)->where('uuid', $xmlUuid)->firstOrFail();

        $request->validate([
            'danfse' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'danfse.required' => 'O arquivo DANFSe é obrigatório.',
            'danfse.mimes'    => 'O DANFSe deve ser um arquivo PDF.',
            'danfse.max'      => 'O arquivo não pode ultrapassar 5 MB.',
        ]);

        $user = Auth::user();
        $file = $request->file('danfse');

        // Calcula bytes que serão liberados (DANFSe anterior) e adicionados (novo)
        $removedBytes = (int) $xml->danfse_file_size;
        $addedBytes   = $file->getSize();
        $netBytes     = $addedBytes - $removedBytes;

        if ($netBytes > 0 && !$this->storage->canUpload($user, $netBytes)) {
            $quota = $this->storage->getQuotaData($user);
            return response()->json([
                'success' => false,
                'message' => "Cota de armazenamento atingida ({$quota['used_mb']} MB / {$quota['quota_mb']} MB). Exclua arquivos para liberar espaço.",
            ], 422);
        }

        // Remove o anterior se existir
        if ($xml->danfse_path) {
            Storage::delete($xml->danfse_path);
        }

        $storagePath = 'invoices/' . Auth::id() . '/' . $invoice->id . '/danfse/' . $file->getClientOriginalName();

        Storage::put($storagePath, file_get_contents($file->getRealPath()));

        $xml->update([
            'danfse_filename'  => $file->getClientOriginalName(),
            'danfse_path'      => $storagePath,
            'danfse_file_size' => $addedBytes,
        ]);

        if ($netBytes > 0) {
            $this->storage->add($user, $netBytes);
        } elseif ($netBytes < 0) {
            $this->storage->remove($user, abs($netBytes));
        }

        InvoiceAuditLog::record(
            $invoice,
            'danfse_importado',
            'DANFSe importado para NF ' . ($xml->invoice_number ?? $xml->filename) . ': ' . $file->getClientOriginalName(),
        );

        return response()->json([
            'success'          => true,
            'message'          => 'DANFSe importado com sucesso.',
            'danfse_filename'  => $xml->danfse_filename,
        ]);
    }

    public function destroyDanfse(string $invoiceUuid, string $xmlUuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $xml = InvoiceXml::where('invoice_id', $invoice->id)->where('uuid', $xmlUuid)->firstOrFail();

        $freedBytes = (int) $xml->danfse_file_size;

        if ($xml->danfse_path) {
            Storage::delete($xml->danfse_path);
        }

        InvoiceAuditLog::record($invoice, 'danfse_removido', 'DANFSe removido da NF ' . ($xml->invoice_number ?? $xml->filename));

        $xml->update(['danfse_filename' => null, 'danfse_path' => null, 'danfse_file_size' => null]);

        $this->storage->remove(Auth::user(), $freedBytes);

        return response()->json(['success' => true, 'message' => 'DANFSe removido com sucesso.']);
    }

    public function viewDanfse(string $invoiceUuid, string $xmlUuid): StreamedResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        $xml = InvoiceXml::where('invoice_id', $invoice->id)->where('uuid', $xmlUuid)->firstOrFail();

        abort_unless($xml->danfse_path && Storage::exists($xml->danfse_path), 404);

        return Storage::response($xml->danfse_path, $xml->danfse_filename, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $xml->danfse_filename . '"',
        ]);
    }
}
