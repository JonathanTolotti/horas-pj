<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceXmlRequest;
use App\Models\Invoice;
use App\Models\InvoiceXml;
use App\Services\XmlParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceXmlController extends Controller
{
    public function __construct(private XmlParserService $parser) {}

    public function store(StoreInvoiceXmlRequest $request, string $invoiceUuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $results = [];
        $allParsed = true;

        foreach ($request->file('xmls') as $file) {
            $storagePath = 'invoices/' . Auth::id() . '/' . $invoice->id . '/' . $file->getClientOriginalName();

            Storage::put($storagePath, file_get_contents($file->getRealPath()));

            $parsed = $this->parser->parse($storagePath);

            $invoiceXml = $invoice->xmls()->create([
                'filename'       => $file->getClientOriginalName(),
                'path'           => $storagePath,
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

            if (!$parsed['xml_parsed']) {
                $allParsed = false;
            }

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

        Storage::delete($xml->path);
        $xml->delete();

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
}
