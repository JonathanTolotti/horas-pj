<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SupervisorAccess;
use App\Models\SupervisorInvitation;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasSubscription;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'tax_id',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function supervisorAccesses(): HasMany
    {
        return $this->hasMany(SupervisorAccess::class);
    }

    public function supervisingAccesses(): HasMany
    {
        return $this->hasMany(SupervisorAccess::class, 'supervisor_id');
    }

    public function pendingSupervisorInvitations(): HasMany
    {
        return $this->hasMany(SupervisorInvitation::class, 'supervisor_id')->where('status', 'pending');
    }

    public function isSupervisorOf(int $userId): bool
    {
        return SupervisorAccess::where('user_id', $userId)
            ->where('supervisor_id', $this->id)
            ->active()
            ->exists();
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
