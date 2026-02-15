<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * IMPORTANT FOR SPATIE
     */
    protected string $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'skip_2fa',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'skip_2fa' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin') || $this->is_admin === true;
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function isMultimediaStaff(): bool
    {
        return $this->hasRole('multimedia_staff');
    }

    public function dashboardRoute(): string
    {
        if ($this->isAdmin()) {
            return 'admin.dashboard';
        }

        if ($this->isMultimediaStaff()) {
            return 'media.dashboard';
        }

        return 'user.dashboard';
    }

    public function notificationSetting(): HasOne
    {
        return $this->hasOne(UserNotificationSetting::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Check if user has an employee record with specific position
     */
    public function hasEmployeeWithPosition(array $positions): bool
    {
        if (!$this->employee) {
            return false;
        }

        $userPosition = strtolower($this->employee->position ?? '');
        
        foreach ($positions as $position) {
            if (str_contains($userPosition, strtolower($position))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is admin (helper method)
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->hasRole('admin') || $this->is_admin === true;
    }

    /**
     * Get user's primary role name
     */
    public function getPrimaryRoleAttribute(): ?string
    {
        return $this->roles->first()?->name;
    }

    /**
     * Get user's display role with proper formatting
     */
    public function getDisplayRoleAttribute(): string
    {
        $role = $this->primary_role;
        
        return match($role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Administrator',
            'finance' => 'Finance Officer',
            'logistics' => 'Logistics Officer',
            'event_manager' => 'Event Manager',
            'multimedia_staff' => 'Multimedia Staff',
            'user' => 'User',
            default => ucfirst(str_replace('_', ' ', $role ?? 'Unknown')),
        };
    }
}
