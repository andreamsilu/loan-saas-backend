<?php

namespace App\Modules\User\Models;

use App\Shared\Enums\UserRole;
use App\Shared\Traits\BelongsToTenant;
use App\Modules\User\Models\RolePermission;
use App\Modules\User\Models\UserRoleAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
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
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::OWNER;
    }

    public function isTenantAdmin(): bool
    {
        return $this->role === UserRole::TENANT_ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::STAFF;
    }

    public function isBorrower(): bool
    {
        return $this->role === UserRole::BORROWER;
    }

    public function hasRole(UserRole|string|array $roles): bool
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }

        $roleValue = $roles instanceof UserRole ? $roles->value : $roles;
        return $this->role->value === $roleValue;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        $tenantId = $this->tenant_id;
        if (!$tenantId) {
            return false;
        }

        $roles = [$this->role->value];

        $extraRoles = UserRoleAssignment::where('tenant_id', $tenantId)
            ->where('user_id', $this->id)
            ->pluck('role')
            ->all();

        if ($extraRoles) {
            $roles = array_values(array_unique(array_merge($roles, $extraRoles)));
        }

        return RolePermission::where('tenant_id', $tenantId)
            ->whereIn('role', $roles)
            ->where('permission', $permission)
            ->exists();
    }
}
