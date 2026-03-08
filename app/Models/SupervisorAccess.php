<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SupervisorAccess extends Model
{
    protected $fillable = [
        'user_id',
        'supervisor_id',
        'can_view_financials',
        'can_view_analytics',
        'can_export',
        'expires_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($model) => $model->uuid = (string) Str::uuid());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $casts = [
        'can_view_financials' => 'boolean',
        'can_view_analytics' => 'boolean',
        'can_export' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return !$this->isExpired();
    }

    public function expiresLabel(): string
    {
        if ($this->expires_at === null) {
            return 'Permanente';
        }

        return $this->expires_at->format('d/m/Y H:i');
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}
