<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceAuditLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'action',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(Invoice $invoice, string $action, string $description, ?array $metadata = null): self
    {
        return self::create([
            'invoice_id'  => $invoice->id,
            'user_id'     => $invoice->user_id,
            'action'      => $action,
            'description' => $description,
            'metadata'    => $metadata,
        ]);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'fatura_criada'     => 'Fatura criada',
            'fatura_atualizada' => 'Fatura editada',
            'status_alterado'   => 'Status alterado',
            'fatura_cancelada'  => 'Fatura cancelada',
            'lancamento_adicionado' => 'Lançamento adicionado',
            'lancamento_atualizado' => 'Lançamento atualizado',
            'lancamento_removido'   => 'Lançamento removido',
            'xml_importado'     => 'XML importado',
            'xml_removido'      => 'XML removido',
            'danfse_importado'  => 'DANFSe importado',
            'danfse_removido'   => 'DANFSe removido',
            'email_enviado'     => 'E-mail enviado',
            default             => $this->action,
        };
    }

    public function actionColor(): string
    {
        return match ($this->action) {
            'fatura_criada'     => 'text-emerald-400',
            'fatura_atualizada' => 'text-blue-400',
            'status_alterado'   => 'text-yellow-400',
            'fatura_cancelada'  => 'text-red-400',
            'lancamento_adicionado' => 'text-emerald-400',
            'lancamento_atualizado' => 'text-blue-400',
            'lancamento_removido'   => 'text-red-400',
            'xml_importado'     => 'text-cyan-400',
            'xml_removido'      => 'text-red-400',
            'danfse_importado'  => 'text-cyan-400',
            'danfse_removido'   => 'text-red-400',
            'email_enviado'     => 'text-purple-400',
            default             => 'text-gray-400',
        };
    }
}
