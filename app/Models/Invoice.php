<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    const STATUS_DRAFT = 'rascunho';
    const STATUS_OPEN = 'aberta';
    const STATUS_RECONCILED = 'conciliada';
    const STATUS_CLOSED = 'encerrada';
    const STATUS_CANCELLED = 'cancelada';

    protected $fillable = [
        'uuid',
        'user_id',
        'company_id',
        'bank_account_id',
        'title',
        'reference_month',
        'status',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(InvoiceEntry::class)->orderBy('sort_order')->orderBy('date');
    }

    public function xmls(): HasMany
    {
        return $this->hasMany(InvoiceXml::class)->orderBy('created_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth($query, string $referenceMonth)
    {
        return $query->where('reference_month', $referenceMonth);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, [self::STATUS_CLOSED, self::STATUS_CANCELLED]);
    }

    public function getTotalCredits(): float
    {
        return (float) $this->entries()->where('type', 'credit')->sum('amount');
    }

    public function getTotalDebits(): float
    {
        return (float) $this->entries()->where('type', 'debit')->sum('amount');
    }

    public function getNetTotal(): float
    {
        return round($this->getTotalCredits() - $this->getTotalDebits(), 2);
    }

    public function getReconcilableTotal(): float
    {
        return (float) $this->entries()->where('reconcile_with_xml', true)->where('type', 'credit')->sum('amount')
             - (float) $this->entries()->where('reconcile_with_xml', true)->where('type', 'debit')->sum('amount');
    }

    public function getXmlTotal(): float
    {
        return (float) $this->xmls()->where('xml_parsed', true)->sum('amount');
    }

    public function getReconciliationStatus(): string
    {
        $reconcilableCents = (int) round($this->getReconcilableTotal() * 100);
        $xmlTotalCents     = (int) round($this->getXmlTotal() * 100);

        if ($xmlTotalCents <= 0) {
            return 'pendente';
        }

        if (abs($reconcilableCents - $xmlTotalCents) <= 1) {
            return 'conciliado';
        }

        return 'parcial';
    }
}
