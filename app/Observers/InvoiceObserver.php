<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\InvoiceAuditLog;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        InvoiceAuditLog::record($invoice, 'fatura_criada', 'Fatura criada: ' . $invoice->title);
    }

    public function updated(Invoice $invoice): void
    {
        if ($invoice->wasChanged('status')) {
            $oldStatus = $invoice->getOriginal('status');
            $newStatus = $invoice->status;

            $labels = [
                'rascunho'   => 'Rascunho',
                'aberta'     => 'Aberta',
                'conciliada' => 'Conciliada',
                'encerrada'  => 'Encerrada',
                'cancelada'  => 'Cancelada',
            ];

            InvoiceAuditLog::record(
                $invoice,
                'status_alterado',
                'Status alterado de "' . ($labels[$oldStatus] ?? $oldStatus) . '" para "' . ($labels[$newStatus] ?? $newStatus) . '"',
                ['de' => $oldStatus, 'para' => $newStatus],
            );

            return;
        }

        $tracked = ['title', 'notes', 'company_id', 'bank_account_id', 'reference_month'];
        $changed = array_intersect(array_keys($invoice->getChanges()), $tracked);

        if (empty($changed)) {
            return;
        }

        InvoiceAuditLog::record($invoice, 'fatura_atualizada', 'Dados da fatura atualizados.');
    }
}
