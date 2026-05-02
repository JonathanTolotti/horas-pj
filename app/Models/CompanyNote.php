<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CompanyNote extends Model
{
    const TYPES = [
        'meeting'     => 'Reunião',
        'negotiation' => 'Negociação',
        'call'        => 'Ligação',
        'email'       => 'E-mail',
        'visit'       => 'Visita',
        'other'       => 'Outro',
    ];

    const TYPE_COLORS = [
        'meeting'     => 'blue',
        'negotiation' => 'purple',
        'call'        => 'green',
        'email'       => 'cyan',
        'visit'       => 'orange',
        'other'       => 'gray',
    ];

    protected $fillable = [
        'user_id',
        'company_id',
        'type',
        'title',
        'content',
        'note_date',
    ];

    protected $casts = [
        'note_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute(): string
    {
        return self::TYPE_COLORS[$this->type] ?? 'gray';
    }
}
