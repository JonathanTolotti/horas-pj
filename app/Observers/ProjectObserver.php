<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Project;

class ProjectObserver
{
    public function created(Project $project): void
    {
        $excluded = AuditLog::SETTING_EXCLUDED_FIELDS;
        $newValues = array_diff_key($project->getAttributes(), array_flip($excluded));

        AuditLog::record(
            userId: $project->user_id,
            entityType: 'project',
            entityId: $project->id,
            entityLabel: $project->name,
            action: 'created',
            oldValues: null,
            newValues: $newValues,
            ipAddress: request()->ip(),
        );
    }

    public function updated(Project $project): void
    {
        $changed = $project->getChanges();
        $excluded = AuditLog::SETTING_EXCLUDED_FIELDS;
        $newValues = array_diff_key($changed, array_flip($excluded));

        if (empty($newValues)) {
            return;
        }

        $oldValues = array_intersect_key($project->getOriginal(), $newValues);

        AuditLog::record(
            userId: $project->user_id,
            entityType: 'project',
            entityId: $project->id,
            entityLabel: $project->name,
            action: 'updated',
            oldValues: $oldValues,
            newValues: $newValues,
            ipAddress: request()->ip(),
        );
    }

    public function deleted(Project $project): void
    {
        AuditLog::record(
            userId: $project->user_id,
            entityType: 'project',
            entityId: $project->id,
            entityLabel: $project->name,
            action: 'deleted',
            oldValues: null,
            newValues: null,
            ipAddress: request()->ip(),
        );
    }
}
