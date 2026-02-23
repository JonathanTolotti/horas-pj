<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangelogItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'changelog_id',
        'category',
        'description',
        'sort_order',
    ];

    public function changelog(): BelongsTo
    {
        return $this->belongsTo(Changelog::class);
    }
}
