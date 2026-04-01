<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Mail\InvoiceEmail;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceAuditLog;
use App\Models\TimeEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $user = Auth::user();

        $query = Invoice::forUser($userId)
            ->with(['company', 'bankAccount']);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('reference_month')) {
            $query->forMonth($request->reference_month);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('bank_account_id')) {
            $bankAccount = BankAccount::forUser($userId)->where('uuid', $request->bank_account_id)->first();
            if ($bankAccount) {
                $query->where('bank_account_id', $bankAccount->id);
            }
        }

        $invoices = $query->orderBy('reference_month', 'desc')->orderBy('created_at', 'desc')->get();

        $companies = Company::forUser($userId)->active()->orderBy('name')->get();
        $bankAccounts = BankAccount::forUser($userId)->active()->orderBy('bank_name')->get();

        $statusOptions = [
            Invoice::STATUS_DRAFT      => 'Rascunho',
            Invoice::STATUS_OPEN       => 'Aberta',
            Invoice::STATUS_RECONCILED => 'Conciliada',
            Invoice::STATUS_CLOSED     => 'Encerrada',
            Invoice::STATUS_CANCELLED  => 'Cancelada',
        ];

        return view('invoices.index', compact(
            'invoices',
            'companies',
            'bankAccounts',
            'statusOptions',
        ));
    }

    public function show(string $uuid): View
    {
        $invoice = Invoice::forUser(Auth::id())
            ->where('uuid', $uuid)
            ->with(['company', 'bankAccount', 'entries', 'xmls', 'auditLogs.user'])
            ->firstOrFail();

        $companies = Company::forUser(Auth::id())->active()->orderBy('name')->get();
        $bankAccounts = BankAccount::forUser(Auth::id())->active()->orderBy('bank_name')->get();

        return view('invoices.show', compact('invoice', 'companies', 'bankAccounts'));
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Resolve bank_account uuid to id
        if (!empty($validated['bank_account_id'])) {
            $ba = BankAccount::forUser(Auth::id())->where('id', $validated['bank_account_id'])->first();
            $validated['bank_account_id'] = $ba?->id;
        }

        $invoice = Invoice::create(array_merge($validated, [
            'user_id' => Auth::id(),
            'status'  => Invoice::STATUS_DRAFT,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Fatura criada com sucesso.',
            'invoice' => $invoice->load(['company', 'bankAccount']),
            'redirect' => route('invoices.show', $invoice->uuid),
        ]);
    }

    public function update(StoreInvoiceRequest $request, string $uuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $validated = $request->validated();

        $invoice->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fatura atualizada com sucesso.',
            'invoice' => $invoice->fresh()->load(['company', 'bankAccount']),
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        if ($invoice->status !== Invoice::STATUS_DRAFT) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas faturas em rascunho podem ser excluídas.',
            ], 422);
        }

        // Desvincular time_entries ao excluir fatura
        TimeEntry::where('invoice_id', $invoice->id)->update(['invoice_id' => null]);

        // Delete XML files from storage
        foreach ($invoice->xmls as $xml) {
            \Illuminate\Support\Facades\Storage::delete($xml->path);
        }

        $invoice->delete();

        return response()->json(['success' => true, 'message' => 'Fatura excluída com sucesso.']);
    }

    public function updateStatus(Request $request, string $uuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        if ($invoice->isClosed()) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser alterada.'], 403);
        }

        $request->validate([
            'status' => ['required', 'in:rascunho,aberta,conciliada,encerrada'],
        ], [
            'status.required' => 'O status é obrigatório.',
            'status.in'       => 'Status inválido.',
        ]);

        $newStatus = $request->status;

        $warning = null;

        if ($newStatus === Invoice::STATUS_CLOSED) {
            $reconciliation = $invoice->getReconciliationStatus();
            if ($reconciliation !== 'conciliado') {
                $warning = 'Atenção: a fatura está sendo encerrada sem conciliação completa com os XMLs.';
            }
        }

        $invoice->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso.',
            'warning' => $warning,
            'invoice' => $invoice->fresh(),
        ]);
    }

    public function cancel(string $uuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return response()->json(['success' => false, 'message' => 'Fatura já está cancelada.'], 422);
        }

        if ($invoice->status === Invoice::STATUS_CLOSED) {
            return response()->json(['success' => false, 'message' => 'Fatura encerrada não pode ser cancelada.'], 403);
        }

        // Liberar os lançamentos vinculados
        TimeEntry::where('invoice_id', $invoice->id)->update(['invoice_id' => null]);

        $invoice->update(['status' => Invoice::STATUS_CANCELLED]);

        InvoiceAuditLog::record($invoice, 'fatura_cancelada', 'Fatura cancelada. Lançamentos liberados.');

        return response()->json([
            'success' => true,
            'message' => 'Fatura cancelada. Os lançamentos foram liberados.',
        ]);
    }

    public function availableTimeEntries(string $uuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        $entries = TimeEntry::forUser(Auth::id())
            ->where('month_reference', $invoice->reference_month)
            ->where(function ($q) use ($invoice) {
                $q->whereNull('invoice_id')->orWhere('invoice_id', $invoice->id);
            })
            ->with('project')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->map(fn($e) => [
                'id'           => $e->id,
                'date'         => $e->date->format('Y-m-d'),
                'date_br'      => $e->date->format('d/m/Y'),
                'hours'        => (float) $e->hours,
                'description'  => $e->description,
                'project_name' => $e->project?->name ?? '—',
                'invoice_id'   => $e->invoice_id,
            ]);

        return response()->json(['success' => true, 'entries' => $entries]);
    }

    public function pdf(string $uuid)
    {
        $invoice = Invoice::forUser(Auth::id())
            ->where('uuid', $uuid)
            ->with(['company', 'bankAccount', 'entries', 'xmls'])
            ->firstOrFail();

        $user = Auth::user();

        $pdf = Pdf::loadView('exports.pdf.invoice', compact('invoice', 'user'))
            ->setPaper('a4', 'portrait');

        $filename = 'fatura-' . $invoice->reference_month . '-' . str_replace(' ', '-', strtolower($invoice->title)) . '.pdf';

        return $pdf->download($filename);
    }

    public function sendEmail(Request $request, string $uuid): JsonResponse
    {
        $invoice = Invoice::forUser(Auth::id())
            ->where('uuid', $uuid)
            ->with(['company', 'bankAccount', 'entries', 'xmls'])
            ->firstOrFail();

        $request->validate([
            'recipient_email' => ['required', 'email'],
            'message'         => ['nullable', 'string', 'max:2000'],
        ], [
            'recipient_email.required' => 'O e-mail do destinatário é obrigatório.',
            'recipient_email.email'    => 'Informe um e-mail válido.',
        ]);

        $user = Auth::user();

        Mail::to($request->recipient_email)
            ->send(new InvoiceEmail($invoice, $user, $request->recipient_email, $request->message ?? ''));

        InvoiceAuditLog::record(
            $invoice,
            'email_enviado',
            'E-mail enviado para ' . $request->recipient_email,
            ['destinatario' => $request->recipient_email],
        );

        return response()->json([
            'success' => true,
            'message' => 'E-mail enviado com sucesso para ' . $request->recipient_email . '.',
        ]);
    }
}
