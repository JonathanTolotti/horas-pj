<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'entity_label',
        'action',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    const SETTING_EXCLUDED_FIELDS = ['id', 'user_id', 'updated_at', 'created_at'];

    const MONETARY_FIELDS = ['hourly_rate', 'extra_value', 'discount_value', 'on_call_hourly_rate'];

    const PERCENTAGE_FIELDS = ['percentage'];

    const FIELD_LABELS = [
        'hourly_rate'         => 'Valor/hora',
        'extra_value'         => 'Adicional mensal',
        'discount_value'      => 'Desconto mensal',
        'on_call_hourly_rate' => 'Valor/hora sobreaviso',
        'auto_save_tracking'  => 'Salvar tracking automaticamente',
        'name'                => 'Nome',
        'active'              => 'Ativo',
        'is_default'          => 'Projeto padrão',
        'default_description' => 'Descrição padrão',
        'cnpj'                => 'CNPJ',
        'percentage'          => 'Percentual',
        'project_id'          => 'Projeto',
        'company_id'          => 'Empresa',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public static function record(
        int $userId,
        string $entityType,
        ?int $entityId,
        ?string $entityLabel,
        string $action,
        ?array $oldValues,
        ?array $newValues,
        ?string $ipAddress = null
    ): self {
        return self::create([
            'user_id'      => $userId,
            'entity_type'  => $entityType,
            'entity_id'    => $entityId,
            'entity_label' => $entityLabel,
            'action'       => $action,
            'old_values'   => $oldValues,
            'new_values'   => $newValues,
            'ip_address'   => $ipAddress,
        ]);
    }

    public static function entityTypeLabel(string $entityType): string
    {
        return match ($entityType) {
            'setting'         => 'Configurações',
            'project'         => 'Projeto',
            'company'         => 'Empresa',
            'company_project' => 'Vínculo Empresa-Projeto',
            default           => $entityType,
        };
    }

    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'created' => 'Criado',
            'updated' => 'Atualizado',
            'deleted' => 'Excluído',
            default   => $action,
        };
    }

    public static function formatFieldValue(string $field, mixed $value): string
    {
        if (is_null($value) || $value === '') return '—';
        if (is_bool($value)) return $value ? 'Sim' : 'Não';
        if (in_array($field, self::MONETARY_FIELDS)) {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        }
        if (in_array($field, self::PERCENTAGE_FIELDS)) {
            return number_format((float) $value, 2, ',', '.') . '%';
        }
        return (string) $value;
    }
}
