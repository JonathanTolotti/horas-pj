<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InvoiceXml extends Model
{
    protected $fillable = [
        'uuid',
        'invoice_id',
        'filename',
        'path',
        'invoice_number',
        'amount',
        'issued_at',
        'provider_cnpj',
        'recipient_cnpj',
        'provider_name',
        'recipient_name',
        'xml_parsed',
        'parse_error',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'amount' => 'decimal:2',
        'xml_parsed' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (InvoiceXml $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
