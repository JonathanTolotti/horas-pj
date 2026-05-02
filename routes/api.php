<?php

use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\OnCallController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\TimeEntryController;
use App\Http\Controllers\Api\V1\TrackingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // Usuário autenticado (sem restrição de ability)
    Route::get('/me', [MeController::class, 'show']);

    // Lançamentos de horas — leitura
    Route::middleware('ability:time-entries:read')->group(function () {
        Route::get('/time-entries', [TimeEntryController::class, 'index']);
        Route::get('/time-entries/stats', [TimeEntryController::class, 'stats']);
    });

    // Lançamentos de horas — escrita
    Route::middleware('ability:time-entries:write')->group(function () {
        Route::post('/time-entries', [TimeEntryController::class, 'store']);
        Route::delete('/time-entries/{id}', [TimeEntryController::class, 'destroy']);
    });

    // Projetos — leitura
    Route::middleware('ability:projects:read')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index']);
    });

    // Projetos — escrita
    Route::middleware('ability:projects:write')->group(function () {
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::put('/projects/{id}', [ProjectController::class, 'update']);
        Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    });

    // Empresas — leitura
    Route::middleware('ability:companies:read')->group(function () {
        Route::get('/companies', [CompanyController::class, 'index']);
        Route::get('/companies/{id}', [CompanyController::class, 'show']);
    });

    // Empresas — escrita
    Route::middleware('ability:companies:write')->group(function () {
        Route::post('/companies', [CompanyController::class, 'store']);
        Route::put('/companies/{id}', [CompanyController::class, 'update']);
        Route::delete('/companies/{id}', [CompanyController::class, 'destroy']);
    });

    // Configurações — leitura
    Route::middleware('ability:settings:read')->group(function () {
        Route::get('/settings', [SettingsController::class, 'show']);
    });

    // Configurações — escrita
    Route::middleware('ability:settings:write')->group(function () {
        Route::put('/settings', [SettingsController::class, 'update']);
    });

    // Tracking
    Route::middleware('ability:tracking')->group(function () {
        Route::get('/tracking', [TrackingController::class, 'status']);
        Route::post('/tracking/start', [TrackingController::class, 'start']);
        Route::post('/tracking/stop', [TrackingController::class, 'stop']);
    });

    // Sobreaviso (Premium) — leitura
    Route::middleware(['premium:on_call', 'ability:on-call:read'])->group(function () {
        Route::get('/on-call', [OnCallController::class, 'index']);
        Route::get('/on-call/stats', [OnCallController::class, 'stats']);
    });

    // Sobreaviso (Premium) — escrita
    Route::middleware(['premium:on_call', 'ability:on-call:write'])->group(function () {
        Route::post('/on-call', [OnCallController::class, 'store']);
        Route::put('/on-call/{id}', [OnCallController::class, 'update']);
        Route::delete('/on-call/{id}', [OnCallController::class, 'destroy']);
    });
});
