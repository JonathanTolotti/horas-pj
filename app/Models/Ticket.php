<?php

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'operator_id', 'title', 'category', 'status',
    ];

    protected $casts = [
        'status'   => TicketStatus::class,
        'category' => TicketCategory::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    public function publicMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)
            ->where('is_internal', false)
            ->orderBy('created_at');
    }

    public function lastPublicMessage(): HasOne
    {
        return $this->hasOne(TicketMessage::class)
            ->where('is_internal', false)
            ->latestOfMany('created_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isClosed(): bool
    {
        return $this->status === TicketStatus::Closed;
    }
}
