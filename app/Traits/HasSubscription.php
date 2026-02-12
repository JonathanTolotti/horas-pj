<?php

namespace App\Traits;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasSubscription
{
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isPremium(): bool
    {
        return $this->subscription?->isPremium() ?? false;
    }

    public function isOnTrial(): bool
    {
        $sub = $this->subscription;
        return $sub && $sub->status === 'trial' && $sub->trial_ends_at?->isFuture();
    }

    public function canUseFeature(string $feature): bool
    {
        $requiredPlan = config("plans.features.{$feature}");

        if (!$requiredPlan || $requiredPlan === 'free') {
            return true;
        }

        return $this->isPremium();
    }

    public function getLimit(string $key): ?int
    {
        $plan = $this->isPremium() ? 'premium' : 'free';
        return config("plans.limits.{$plan}.{$key}");
    }

    public function createTrialSubscription(): Subscription
    {
        return $this->subscription()->create([
            'plan' => 'premium',
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(config('plans.trial_days')),
        ]);
    }

    public function activatePremium(int $months): Subscription
    {
        $subscription = $this->subscription;

        if (!$subscription) {
            $subscription = $this->subscription()->create([
                'plan' => 'premium',
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonths($months),
            ]);
        } else {
            // Se já tem assinatura ativa, estende
            $startsAt = $subscription->ends_at?->isFuture() ? $subscription->ends_at : now();

            $subscription->update([
                'plan' => 'premium',
                'status' => 'active',
                'starts_at' => $subscription->starts_at ?? now(),
                'ends_at' => $startsAt->copy()->addMonths($months),
            ]);
        }

        return $subscription->fresh();
    }

    public function getSubscriptionBadge(): array
    {
        $sub = $this->subscription;

        if (!$sub) {
            return ['label' => 'Free', 'color' => 'gray'];
        }

        if ($sub->status === 'trial' && $sub->trial_ends_at?->isFuture()) {
            $days = $sub->daysRemaining();
            return ['label' => "Trial ({$days}d)", 'color' => 'yellow'];
        }

        if ($sub->isPremium()) {
            $days = $sub->daysRemaining();
            if ($days <= 7) {
                return ['label' => "Premium ({$days}d)", 'color' => 'orange'];
            }
            return ['label' => 'Premium', 'color' => 'green'];
        }

        return ['label' => 'Free', 'color' => 'gray'];
    }
}
