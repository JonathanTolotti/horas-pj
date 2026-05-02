<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ConsolidationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceEntryController;
use App\Http\Controllers\InvoiceXmlController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\SupervisorAccessController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\MonthlyAdjustmentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\OnCallController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminTicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

// Autenticação de Dois Fatores
Route::middleware('auth')->group(function () {
    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/two-factor', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('/two-factor/resend', [TwoFactorController::class, 'resend'])->name('two-factor.resend');
    Route::post('/settings/two-factor-toggle', [SettingsController::class, 'toggleTwoFactor'])->name('settings.two-factor-toggle');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [TimeEntryController::class, 'index'])->name('dashboard');
    Route::post('/time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::delete('/time-entries/{timeEntry}', [TimeEntryController::class, 'destroy'])->name('time-entries.destroy');
    Route::get('/time-entries/stats', [TimeEntryController::class, 'stats'])->name('time-entries.stats');

    // Monthly Adjustments
    Route::put('/monthly-adjustments/{month}', [MonthlyAdjustmentController::class, 'update'])
        ->name('monthly-adjustments.update');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'updateSettings'])->name('settings.update');
    Route::get('/settings/audit-logs', [SettingsController::class, 'auditLogsPartial'])->name('settings.audit-logs');

    // Projects
    Route::post('/projects', [SettingsController::class, 'storeProject'])->name('projects.store');
    Route::put('/projects/{project}', [SettingsController::class, 'updateProject'])->name('projects.update');
    Route::delete('/projects/{project}', [SettingsController::class, 'destroyProject'])->name('projects.destroy');

    // Companies (CRM)
    Route::get('/cnpj/{cnpj}', [CompanyController::class, 'lookupCnpj'])->name('cnpj.lookup');
    Route::get('/cep/{cep}', [CompanyController::class, 'lookupCep'])->name('cep.lookup');
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    Route::post('/companies/{company}/projects', [CompanyController::class, 'attachProject'])->name('companies.projects.attach');
    Route::put('/companies/{company}/projects/{project}', [CompanyController::class, 'updateProjectPercentage'])->name('companies.projects.update');
    Route::delete('/companies/{company}/projects/{project}', [CompanyController::class, 'detachProject'])->name('companies.projects.detach');
    Route::post('/companies/{company}/documents', [CompanyController::class, 'storeDocument'])->name('companies.documents.store');
    Route::get('/companies/{company}/documents/{document}/view', [CompanyController::class, 'viewDocument'])->name('companies.documents.view');
    Route::get('/companies/{company}/documents/{document}/download', [CompanyController::class, 'downloadDocument'])->name('companies.documents.download');
    Route::delete('/companies/{company}/documents/{document}', [CompanyController::class, 'destroyDocument'])->name('companies.documents.destroy');
    Route::post('/companies/{company}/notes', [CompanyController::class, 'storeNote'])->name('companies.notes.store');
    Route::put('/companies/{company}/notes/{note}', [CompanyController::class, 'updateNote'])->name('companies.notes.update');
    Route::delete('/companies/{company}/notes/{note}', [CompanyController::class, 'destroyNote'])->name('companies.notes.destroy');

    // Company-Project relationship (settings page — project-centric)
    Route::post('/projects/{project}/companies', [SettingsController::class, 'attachCompany'])->name('projects.companies.attach');
    Route::put('/projects/{project}/companies/{company}', [SettingsController::class, 'updateCompanyPercentage'])->name('projects.companies.update');
    Route::delete('/projects/{project}/companies/{company}', [SettingsController::class, 'detachCompany'])->name('projects.companies.detach');

    // Tracking
    Route::get('/tracking/status', [TrackingController::class, 'status'])->name('tracking.status');
    Route::post('/tracking/start', [TrackingController::class, 'start'])->name('tracking.start');
    Route::post('/tracking/stop', [TrackingController::class, 'stop'])->name('tracking.stop');

    // On-Call (Sobreaviso) - apenas leitura para todos
    Route::get('/on-call', [OnCallController::class, 'index'])->name('on-call.index');
    Route::get('/on-call/stats', [OnCallController::class, 'stats'])->name('on-call.stats');

    // Avisos e Lembretes
    Route::prefix('notices')->group(function () {
        Route::get('/', [NoticeController::class, 'index'])->name('notices.index');
        Route::post('/', [NoticeController::class, 'store'])->name('notices.store');
        Route::put('/{notice}', [NoticeController::class, 'update'])->name('notices.update');
        Route::delete('/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
        Route::post('/{notice}/dismiss', [NoticeController::class, 'dismiss'])->name('notices.dismiss');
    });

    // Changelog
    Route::get('/changelogs/unread-count', [ChangelogController::class, 'unreadCount'])->name('changelogs.unread-count');
    Route::post('/changelogs/mark-all-read', [ChangelogController::class, 'markAllRead'])->name('changelogs.mark-all-read');
    Route::post('/changelogs/{changelog}/read', [ChangelogController::class, 'markRead'])->name('changelogs.read');

    // Reports
    Route::get('/reports', [ExportController::class, 'index'])->name('reports.index');

    // Analytics (Premium)
    Route::prefix('analytics')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/summary', [AnalyticsController::class, 'summary'])->name('analytics.summary');
        Route::get('/monthly-comparison', [AnalyticsController::class, 'monthlyComparison'])->name('analytics.monthly');
        Route::get('/hours-by-weekday', [AnalyticsController::class, 'hoursByWeekday'])->name('analytics.weekday');
        Route::get('/hours-by-project', [AnalyticsController::class, 'hoursByProject'])->name('analytics.project');
        Route::get('/revenue-trend', [AnalyticsController::class, 'revenueTrend'])->name('analytics.trend');
        Route::get('/monthly-projection', [AnalyticsController::class, 'monthlyProjection'])->name('analytics.projection');
    });
});

// Chamados de suporte (usuário)
Route::middleware(['auth', 'verified'])->prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::get('/create', [TicketController::class, 'create'])->name('create');
    Route::post('/', [TicketController::class, 'store'])->name('store');
    Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
    Route::post('/{ticket}/messages', [TicketController::class, 'addMessage'])->name('messages.store');
    Route::post('/{ticket}/close', [TicketController::class, 'close'])->name('close');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas de assinatura (autenticado)
Route::middleware(['auth', 'verified'])->prefix('subscription')->group(function () {
    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::get('/checkout/{months}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/manage', [SubscriptionController::class, 'manage'])->name('subscription.manage');
    Route::get('/check-payment/{payment}', [SubscriptionController::class, 'checkPaymentStatus'])->name('subscription.check');
    Route::get('/receipt/{payment}', [SubscriptionController::class, 'receipt'])->name('subscription.receipt');
});

// Webhook (sem auth, mas com token secreto na URL)
Route::post('/webhook/abacatepay', [SubscriptionController::class, 'webhook'])
    ->name('webhook.abacatepay');

// Consolidação de período (Premium)
Route::middleware(['auth', 'verified', 'premium:export_pdf'])->group(function () {
    Route::get('/consolidation', [ConsolidationController::class, 'index'])->name('consolidation.index');
    Route::post('/consolidation/filter', [ConsolidationController::class, 'filter'])->name('consolidation.filter');
    Route::post('/consolidation/clear', [ConsolidationController::class, 'clear'])->name('consolidation.clear');
    Route::post('/consolidation/pdf', [ConsolidationController::class, 'pdf'])->name('consolidation.pdf');
});

// Rotas de exportação (Premium)
Route::middleware(['auth', 'verified', 'premium:export_pdf'])->prefix('export')->group(function () {
    Route::get('/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
    Route::get('/nf', [ExportController::class, 'nf'])->name('export.nf');
    Route::get('/annual', [ExportController::class, 'annualReport'])->name('export.annual');
});

Route::middleware(['auth', 'verified', 'premium:export_excel'])->group(function () {
    Route::get('/export/excel', [ExportController::class, 'excel'])->name('export.excel');
    Route::get('/export/csv-importable', [ExportController::class, 'csvImportable'])->name('export.csv-importable');
});

// Rotas de importacao (Premium)
Route::middleware(['auth', 'verified', 'premium:import_csv'])->prefix('import')->group(function () {
    Route::post('/csv/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('/csv', [ImportController::class, 'import'])->name('import.csv');
});

// Rotas de Sobreaviso (Premium)
Route::middleware(['auth', 'verified', 'premium:on_call'])->group(function () {
    Route::post('/on-call', [OnCallController::class, 'store'])->name('on-call.store');
    Route::put('/on-call/{onCall}', [OnCallController::class, 'update'])->name('on-call.update');
    Route::delete('/on-call/{onCall}', [OnCallController::class, 'destroy'])->name('on-call.destroy');
    Route::post('/on-call/recalculate', [OnCallController::class, 'recalculate'])->name('on-call.recalculate');
});

// Supervisores - gestão pelo dono dos dados (premium)
Route::middleware(['auth', 'verified', 'premium:supervisor'])->group(function () {
    Route::prefix('settings/supervisors')->group(function () {
        Route::get('/', [SupervisorAccessController::class, 'index'])->name('supervisors.index');
        Route::post('/invite', [SupervisorAccessController::class, 'invite'])->name('supervisors.invite');
        Route::patch('/{supervisorAccess}', [SupervisorAccessController::class, 'update'])->name('supervisors.update');
        Route::delete('/{supervisorAccess}', [SupervisorAccessController::class, 'destroy'])->name('supervisors.destroy');
        Route::delete('/invitations/{supervisorInvitation}', [SupervisorAccessController::class, 'cancelInvite'])->name('supervisors.invitations.cancel');
    });

    // Área do supervisor - convites
    Route::prefix('supervisor')->group(function () {
        Route::get('/', [SupervisorController::class, 'index'])->name('supervisor.index');
        Route::get('/invitations', [SupervisorController::class, 'invitations'])->name('supervisor.invitations');
        Route::post('/invitations/{supervisorInvitation}/accept', [SupervisorController::class, 'accept'])->name('supervisor.invitations.accept');
        Route::post('/invitations/{supervisorInvitation}/reject', [SupervisorController::class, 'reject'])->name('supervisor.invitations.reject');
    });

    // Área do supervisor - visualização (requer acesso específico via UUID)
    Route::middleware(['supervisor.access'])->prefix('supervisor')->group(function () {
        Route::get('/{access}', [SupervisorController::class, 'show'])->name('supervisor.show');
        Route::get('/{access}/export/pdf', [SupervisorController::class, 'exportPdf'])->name('supervisor.export.pdf');
        Route::get('/{access}/export/excel', [SupervisorController::class, 'exportExcel'])->name('supervisor.export.excel');
    });
});

// Faturamento (Premium)
Route::middleware(['auth', 'verified', 'premium:billing'])->group(function () {
    // Contas Bancárias
    Route::prefix('bank-accounts')->name('bank-accounts.')->group(function () {
        Route::get('/', [BankAccountController::class, 'index'])->name('index');
        Route::post('/', [BankAccountController::class, 'store'])->name('store');
        Route::put('/{uuid}', [BankAccountController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [BankAccountController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/toggle', [BankAccountController::class, 'toggle'])->name('toggle');
    });

    // Faturas
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{uuid}', [InvoiceController::class, 'show'])->name('show');
        Route::put('/{uuid}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{uuid}/cancel', [InvoiceController::class, 'cancel'])->name('cancel');
        Route::get('/{uuid}/pdf', [InvoiceController::class, 'pdf'])->name('pdf');
        Route::get('/{uuid}/available-time-entries', [InvoiceController::class, 'availableTimeEntries'])->name('available-time-entries');
        Route::post('/{uuid}/send-email', [InvoiceController::class, 'sendEmail'])->name('send-email');

        // Lançamentos
        Route::post('/{invoiceUuid}/entries', [InvoiceEntryController::class, 'store'])->name('entries.store');
        Route::put('/{invoiceUuid}/entries/{entryUuid}', [InvoiceEntryController::class, 'update'])->name('entries.update');
        Route::delete('/{invoiceUuid}/entries/{entryUuid}', [InvoiceEntryController::class, 'destroy'])->name('entries.destroy');

        // XMLs
        Route::post('/{invoiceUuid}/xmls', [InvoiceXmlController::class, 'store'])->name('xmls.store');
        Route::delete('/{invoiceUuid}/xmls/{xmlUuid}', [InvoiceXmlController::class, 'destroy'])->name('xmls.destroy');
        Route::get('/{invoiceUuid}/xmls/{xmlUuid}/download', [InvoiceXmlController::class, 'download'])->name('xmls.download');

        // DANFSe
        Route::post('/{invoiceUuid}/xmls/{xmlUuid}/danfse', [InvoiceXmlController::class, 'uploadDanfse'])->name('xmls.danfse.upload');
        Route::delete('/{invoiceUuid}/xmls/{xmlUuid}/danfse', [InvoiceXmlController::class, 'destroyDanfse'])->name('xmls.danfse.destroy');
        Route::get('/{invoiceUuid}/xmls/{xmlUuid}/danfse', [InvoiceXmlController::class, 'viewDanfse'])->name('xmls.danfse.view');
    });
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::post('/users/{user}/activate-premium', [AdminController::class, 'activatePremium'])->name('admin.users.activate-premium');

    // Chamados de suporte (admin)
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
    Route::post('/tickets/{ticket}/messages', [AdminTicketController::class, 'reply'])->name('admin.tickets.messages.store');
    Route::post('/tickets/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('admin.tickets.assign');
    Route::put('/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('admin.tickets.status');

    Route::get('/changelogs', [ChangelogController::class, 'adminIndex'])->name('admin.changelogs.index');
    Route::post('/changelogs', [ChangelogController::class, 'store'])->name('admin.changelogs.store');
    Route::put('/changelogs/{changelog}', [ChangelogController::class, 'update'])->name('admin.changelogs.update');
    Route::delete('/changelogs/{changelog}', [ChangelogController::class, 'destroy'])->name('admin.changelogs.destroy');
});

require __DIR__.'/auth.php';
