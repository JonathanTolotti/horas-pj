<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceEntryRequest;
use App\Models\Invoice;
use App\Models\InvoiceEntry;
use App\Models\TimeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class InvoiceEntryController extends Controller
{
    public function store(StoreInvoiceEntryRequest $request, string $invoiceUuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $validated = $request->validated();

        // Se vier vinculado a um time_entry único, verificar se já está em outra fatura
        if (!empty($validated['time_entry_id'])) {
            $timeEntry = TimeEntry::forUser(Auth::id())->find($validated['time_entry_id']);
            if (!$timeEntry) {
                return response()->json(['success' => false, 'message' => 'Lançamento de horas não encontrado.'], 404);
            }
            if ($timeEntry->invoice_id && $timeEntry->invoice_id !== $invoice->id) {
                return response()->json(['success' => false, 'message' => 'Este lançamento já está vinculado a outra fatura.'], 422);
            }
        }

        // Se vier lista de time_entries (consolidação), verificar se algum já está faturado
        $timeEntryIds = array_filter((array) ($validated['time_entry_ids'] ?? []));
        if (!empty($timeEntryIds)) {
            $alreadyLinked = TimeEntry::forUser(Auth::id())
                ->whereIn('id', $timeEntryIds)
                ->whereNotNull('invoice_id')
                ->exists();
            if ($alreadyLinked) {
                return response()->json(['success' => false, 'message' => 'Um ou mais lançamentos já estão vinculados a uma fatura. Desmarque os itens com o badge "Faturado" e tente novamente.'], 422);
            }
        }

        $entry = $invoice->entries()->create($validated);

        // Vincular o invoice_id no time_entry único
        if (!empty($validated['time_entry_id'])) {
            TimeEntry::where('id', $validated['time_entry_id'])->update(['invoice_id' => $invoice->id]);
        }

        // Vincular o invoice_id em todos os time_entries da consolidação
        if (!empty($timeEntryIds)) {
            TimeEntry::whereIn('id', $timeEntryIds)->update(['invoice_id' => $invoice->id]);
        }

        return response()->json([
            'success'          => true,
            'message'          => 'Lançamento adicionado com sucesso.',
            'entry'            => $entry->load('timeEntry'),
            'totals'           => $this->getTotals($invoice),
            'linked_entry_ids' => $timeEntryIds,
            'invoice_id'       => $invoice->id,
        ]);
    }

    public function update(StoreInvoiceEntryRequest $request, string $invoiceUuid, string $entryUuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $entry = InvoiceEntry::where('invoice_id', $invoice->id)->where('uuid', $entryUuid)->firstOrFail();

        $validated = $request->validated();

        // Se mudou o time_entry_id, verificar conflito
        $oldTimeEntryId = $entry->time_entry_id;
        if (!empty($validated['time_entry_id']) && $validated['time_entry_id'] != $oldTimeEntryId) {
            $timeEntry = TimeEntry::forUser(Auth::id())->find($validated['time_entry_id']);
            if (!$timeEntry) {
                return response()->json(['success' => false, 'message' => 'Lançamento de horas não encontrado.'], 404);
            }
            if ($timeEntry->invoice_id && $timeEntry->invoice_id !== $invoice->id) {
                return response()->json(['success' => false, 'message' => 'Este lançamento já está vinculado a outra fatura.'], 422);
            }
            // Liberar time_entry antigo
            if ($oldTimeEntryId) {
                TimeEntry::where('id', $oldTimeEntryId)->update(['invoice_id' => null]);
            }
            TimeEntry::where('id', $validated['time_entry_id'])->update(['invoice_id' => $invoice->id]);
        } elseif (array_key_exists('time_entry_id', $validated) && empty($validated['time_entry_id']) && $oldTimeEntryId) {
            // Desvinculou
            TimeEntry::where('id', $oldTimeEntryId)->update(['invoice_id' => null]);
        }

        $entry->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lançamento atualizado com sucesso.',
            'entry'   => $entry->fresh()->load('timeEntry'),
            'totals'  => $this->getTotals($invoice),
        ]);
    }

    public function destroy(string $invoiceUuid, string $entryUuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $invoiceUuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $entry = InvoiceEntry::where('invoice_id', $invoice->id)->where('uuid', $entryUuid)->firstOrFail();

        // Desvincular time_entry ao remover
        if ($entry->time_entry_id) {
            TimeEntry::where('id', $entry->time_entry_id)->update(['invoice_id' => null]);
        }

        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lançamento removido com sucesso.',
            'totals'  => $this->getTotals($invoice->fresh()),
        ]);
    }

    private function getTotals(Invoice $invoice): array
    {
        return [
            'total_credits'      => $invoice->getTotalCredits(),
            'total_debits'       => $invoice->getTotalDebits(),
            'net_total'          => $invoice->getNetTotal(),
            'reconcilable_total' => $invoice->getReconcilableTotal(),
            'xml_total'          => $invoice->getXmlTotal(),
            'reconciliation'     => $invoice->getReconciliationStatus(),
        ];
    }
}
