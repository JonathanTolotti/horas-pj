<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Setting;

class SettingObserver
{
    public function updated(Setting $setting): void
    {
        $changed = $setting->getChanges();
        $newValues = array_diff_key($changed, array_flip(AuditLog::SETTING_EXCLUDED_FIELDS));

        if (empty($newValues)) {
            return;
        }

        $oldValues = array_intersect_key($setting->getOriginal(), $newValues);

        AuditLog::record(
            userId: $setting->user_id,
            entityType: 'setting',
            entityId: $setting->id,
            entityLabel: 'Configurações Gerais',
            action: 'updated',
            oldValues: $oldValues,
            newValues: $newValues,
            ipAddress: request()->ip(),
        );
    }
}
