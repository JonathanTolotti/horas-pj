<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
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

    // Tracking
    Route::get('/tracking/status', [TrackingController::class, 'status'])->name('tracking.status');
    Route::post('/tracking/start', [TrackingController::class, 'start'])->name('tracking.start');
    Route::post('/tracking/stop', [TrackingController::class, 'stop'])->name('tracking.stop');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
