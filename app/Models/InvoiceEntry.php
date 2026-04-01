<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InvoiceEntry extends Model
{
    protected $fillable = [
        'uuid',
        'invoice_id',
        'time_entry_id',
        'type',
        'description',
        'amount',
        'date',
        'reconcile_with_xml',
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'reconcile_with_xml' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (InvoiceEntry $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function timeEntry(): BelongsTo
    {
        return $this->belongsTo(TimeEntry::class);
    }

    public function getSignedAmount(): float
    {
        return $this->type === 'credit' ? (float) $this->amount : -(float) $this->amount;
    }
}
