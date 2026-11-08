<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InvoiceXml extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSED = 'processed';
    const STATUS_ERROR = 'error';

    protected $fillable = [
        'uuid',
        'invoice_id',
        'user_id',
        'company_id',
        'status',
        'access_key',
        'competence',
        'filename',
        'path',
        'invoice_number',
        'series',
        'amount',
        'gross_amount',
        'iss_amount',
        'iss_retained',
        'service_code',
        'service_description',
        'municipality_code',
        'issued_at',
        'provider_cnpj',
        'recipient_cnpj',
        'provider_name',
        'recipient_name',
        'xml_parsed',
        'parse_error',
        'xml_file_size',
        'danfse_filename',
        'danfse_path',
        'danfse_file_size',
        'attempts',
        'processed_at',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'iss_amount' => 'decimal:2',
        'iss_retained' => 'boolean',
        'xml_parsed' => 'boolean',
    ];

    protected $hidden = [
        'path',
        'danfse_path',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCompetence($query, string $competence)
    {
        return $query->where('competence', $competence);
    }

    public function scopeQueued($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function isQueued(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * DANFSe só existe para NFS-e Nacional, cuja chave de acesso tem 50 dígitos
     * (NF-e tem 44; ABRASF usa chave composta com hífens).
     */
    public function supportsDanfse(): bool
    {
        return (bool) $this->xml_parsed
            && is_string($this->access_key)
            && strlen($this->access_key) === 50
            && ctype_digit($this->access_key);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'Aguardando processamento',
            self::STATUS_PROCESSING => 'Processando',
            self::STATUS_PROCESSED  => 'Processada',
            self::STATUS_ERROR      => 'Requer atenção',
            default                 => $this->status,
        };
    }
}
