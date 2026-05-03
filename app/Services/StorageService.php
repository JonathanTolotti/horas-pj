<?php

namespace App\Services;

use App\Models\User;

class StorageService
{
    public function getUsed(User $user): int
    {
        return (int) $user->storage_used;
    }

    public function getQuota(User $user): int
    {
        return (int) $user->storage_quota;
    }

    public function canUpload(User $user, int $bytes): bool
    {
        return ($user->storage_used + $bytes) <= $user->storage_quota;
    }

    public function add(User $user, int $bytes): void
    {
        $user->increment('storage_used', $bytes);
    }

    public function remove(User $user, int $bytes): void
    {
        if ($bytes <= 0) {
            return;
        }
        $newValue = max(0, $user->storage_used - $bytes);
        $user->update(['storage_used' => $newValue]);
    }

    public function getQuotaData(User $user): array
    {
        $used  = (int) $user->storage_used;
        $quota = (int) $user->storage_quota;
        $pct   = $quota > 0 ? min(100, round(($used / $quota) * 100, 1)) : 0;

        if ($pct >= 90) {
            $colorClass = 'bg-red-500';
            $textColor  = 'text-red-400';
        } elseif ($pct >= 70) {
            $colorClass = 'bg-yellow-500';
            $textColor  = 'text-yellow-400';
        } else {
            $colorClass = 'bg-emerald-500';
            $textColor  = 'text-emerald-400';
        }

        return [
            'used_bytes'  => $used,
            'quota_bytes' => $quota,
            'used_mb'     => number_format($used / 1048576, 1, ',', '.'),
            'quota_mb'    => number_format($quota / 1048576, 0, ',', '.'),
            'percentage'  => $pct,
            'color_class' => $colorClass,
            'text_color'  => $textColor,
            'full'        => $used >= $quota,
            'near_full'   => $pct >= 90,
        ];
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 1, ',', '.') . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', '.') . ' KB';
        }
        return $bytes . ' B';
    }
}
