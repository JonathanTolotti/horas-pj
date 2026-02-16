<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\OnCallController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [TimeEntryController::class, 'index'])->name('dashboard');
    Route::post('/time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::delete('/time-entries/{timeEntry}', [TimeEntryController::class, 'destroy'])->name('time-entries.destroy');
    Route::get('/time-entries/stats', [TimeEntryController::class, 'stats'])->name('time-entries.stats');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'updateSettings'])->name('settings.update');

    // Projects
    Route::post('/projects', [SettingsController::class, 'storeProject'])->name('projects.store');
    Route::put('/projects/{project}', [SettingsController::class, 'updateProject'])->name('projects.update');
    Route::delete('/projects/{project}', [SettingsController::class, 'destroyProject'])->name('projects.destroy');

    // Companies
    Route::post('/companies', [SettingsController::class, 'storeCompany'])->name('companies.store');
    Route::put('/companies/{company}', [SettingsController::class, 'updateCompany'])->name('companies.update');
    Route::delete('/companies/{company}', [SettingsController::class, 'destroyCompany'])->name('companies.destroy');

    // Company-Project relationship
    Route::post('/projects/{project}/companies', [SettingsController::class, 'attachCompany'])->name('projects.companies.attach');
    Route::put('/projects/{project}/companies/{company}', [SettingsController::class, 'updateCompanyPercentage'])->name('projects.companies.update');
    Route::delete('/projects/{project}/companies/{company}', [SettingsController::class, 'detachCompany'])->name('projects.companies.detach');

    // Tracking
    Route::get('/tracking/status', [TrackingController::class, 'status'])->name('tracking.status');
    Route::post('/tracking/start', [TrackingController::class, 'start'])->name('tracking.start');
    Route::post('/tracking/stop', [TrackingController::class, 'stop'])->name('tracking.stop');

    // On-Call (Sobreaviso)
    Route::get('/on-call', [OnCallController::class, 'index'])->name('on-call.index');
    Route::post('/on-call', [OnCallController::class, 'store'])->name('on-call.store');
    Route::put('/on-call/{onCall}', [OnCallController::class, 'update'])->name('on-call.update');
    Route::delete('/on-call/{onCall}', [OnCallController::class, 'destroy'])->name('on-call.destroy');
    Route::get('/on-call/stats', [OnCallController::class, 'stats'])->name('on-call.stats');
    Route::post('/on-call/recalculate', [OnCallController::class, 'recalculate'])->name('on-call.recalculate');

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
    });
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

// Rotas de exportação (Premium)
Route::middleware(['auth', 'verified', 'premium:export_pdf'])->prefix('export')->group(function () {
    Route::get('/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
    Route::get('/nf', [ExportController::class, 'nf'])->name('export.nf');
    Route::get('/annual', [ExportController::class, 'annualReport'])->name('export.annual');
});

Route::middleware(['auth', 'verified', 'premium:export_excel'])->group(function () {
    Route::get('/export/excel', [ExportController::class, 'excel'])->name('export.excel');
});

// Rotas de importacao (Premium)
Route::middleware(['auth', 'verified', 'premium:import_csv'])->prefix('import')->group(function () {
    Route::post('/csv/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('/csv', [ImportController::class, 'import'])->name('import.csv');
});

require __DIR__.'/auth.php';
