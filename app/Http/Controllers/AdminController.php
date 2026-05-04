<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Subscription;
use App\Models\TimeEntry;
use App\Models\User;
use App\Services\StorageService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'   => User::count(),
            'premium'       => Subscription::where('status', 'active')->where('ends_at', '>', now())->count(),
            'trial'         => Subscription::where('status', 'trial')->where('trial_ends_at', '>', now())->count(),
            'new_30d'       => User::where('created_at', '>=', now()->subDays(30))->count(),
            'active_30d'    => TimeEntry::where('created_at', '>=', now()->subDays(30))->distinct('user_id')->count('user_id'),
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
        ];
        $stats['gratuitos'] = $stats['total_users'] - $stats['premium'] - $stats['trial'];

        return view('admin.dashboard', compact('stats'));
    }

    public function users(Request $request)
    {
        $query = User::with('subscription')->latest();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($plan = $request->input('plan')) {
            if ($plan === 'premium') {
                $query->whereHas('subscription', fn($q) =>
                    $q->where('status', 'active')->where('ends_at', '>', now())
                );
            } elseif ($plan === 'trial') {
                $query->whereHas('subscription', fn($q) =>
                    $q->where('status', 'trial')->where('trial_ends_at', '>', now())
                );
            } elseif ($plan === 'free') {
                $query->whereDoesntHave('subscription', fn($q) =>
                    $q->where('status', 'active')->where('ends_at', '>', now())
                      ->orWhere(fn($q2) => $q2->where('status', 'trial')->where('trial_ends_at', '>', now()))
                );
            }
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function showUser(User $user, StorageService $storage)
    {
        $user->load('subscription');
        $payments = $user->payments()->latest()->get();
        $recentEntries = $user->timeEntries()->with('project')->latest()->limit(10)->get();
        $totalHours = $user->timeEntries()->sum('hours');
        $totalEntries = $user->timeEntries()->count();
        $paymentLogs = PaymentLog::where('user_id', $user->id)
            ->latest()
            ->get()
            ->groupBy('payment_id');
        $tokens = $user->tokens()->latest()->get();
        $storageData = $storage->getQuotaData($user);

        return view('admin.users.show', compact('user', 'payments', 'recentEntries', 'totalHours', 'totalEntries', 'paymentLogs', 'tokens', 'storageData'));
    }

    public function updateStorageQuota(Request $request, User $user)
    {
        $request->validate([
            'quota_mb' => 'required|integer|min:1|max:102400',
        ]);

        $user->update(['storage_quota' => $request->input('quota_mb') * 1048576]);

        return response()->json(['success' => true, 'quota_mb' => $request->input('quota_mb')]);
    }

    public function revokeToken(User $user, int $tokenId)
    {
        $deleted = $user->tokens()->where('id', $tokenId)->delete();

        return response()->json(['success' => $deleted > 0]);
    }

    public function revokeAllTokens(User $user)
    {
        $user->tokens()->delete();

        return response()->json(['success' => true]);
    }

    public function disable2fa(User $user)
    {
        $user->update(['two_factor_enabled' => false]);
        \DB::table('two_factor_codes')->where('user_id', $user->id)->delete();

        return response()->json(['success' => true]);
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);

        return response()->json(['success' => true, 'is_admin' => $user->is_admin]);
    }

    public function activatePremium(Request $request, User $user)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:24',
        ]);

        $user->activatePremium($request->input('months'));

        return response()->json(['success' => true]);
    }
}
