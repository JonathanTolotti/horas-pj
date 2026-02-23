<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Changelog extends Model
{
    protected $fillable = [
        'title',
        'version',
        'notification_style',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ChangelogRead::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChangelogItem::class)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUnreadBy($query, int $userId)
    {
        return $query->whereDoesntHave('reads', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
