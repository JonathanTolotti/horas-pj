<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Company;

class CompanyObserver
{
    public function created(Company $company): void
    {
        $excluded = AuditLog::SETTING_EXCLUDED_FIELDS;
        $newValues = array_diff_key($company->getAttributes(), array_flip($excluded));

        AuditLog::record(
            userId: $company->user_id,
            entityType: 'company',
            entityId: $company->id,
            entityLabel: $company->name,
            action: 'created',
            oldValues: null,
            newValues: $newValues,
            ipAddress: request()->ip(),
        );
    }

    public function updated(Company $company): void
    {
        $changed = $company->getChanges();
        $excluded = AuditLog::SETTING_EXCLUDED_FIELDS;
        $newValues = array_diff_key($changed, array_flip($excluded));

        if (empty($newValues)) {
            return;
        }

        $oldValues = array_intersect_key($company->getOriginal(), $newValues);

        AuditLog::record(
            userId: $company->user_id,
            entityType: 'company',
            entityId: $company->id,
            entityLabel: $company->name,
            action: 'updated',
            oldValues: $oldValues,
            newValues: $newValues,
            ipAddress: request()->ip(),
        );
    }

    public function deleted(Company $company): void
    {
        AuditLog::record(
            userId: $company->user_id,
            entityType: 'company',
            entityId: $company->id,
            entityLabel: $company->name,
            action: 'deleted',
            oldValues: null,
            newValues: null,
            ipAddress: request()->ip(),
        );
    }
}
