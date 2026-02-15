<?php

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Verificar assinaturas expiradas diariamente
Schedule::call(function () {
    Subscription::where('status', 'active')
        ->where('ends_at', '<', now())
        ->update(['status' => 'expired', 'plan' => 'free']);

    Subscription::where('status', 'trial')
        ->where('trial_ends_at', '<', now())
        ->update(['status' => 'expired', 'plan' => 'free']);
})->daily()->name('check-expired-subscriptions');

// Marcar pagamentos Pix expirados a cada minuto
Schedule::call(function () {
    Payment::where('status', 'pending')
        ->where('expires_at', '<', now())
        ->update(['status' => 'expired']);
})->everyMinute()->name('expire-pending-payments');
