<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'designation',
        'company_id',
        'current_company_id',
        'permissions',
        'timezone',
        'is_active',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'permissions' => 'array',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function currentCompany()
    {
        return $this->belongsTo(Company::class, 'current_company_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withPivot('company_id');
    }

    // Role Methods
    public function hasRole(string $roleName, ?int $companyId = null): bool
    {
        $query = $this->roles()->where('name', $roleName);

        if ($companyId) {
            $query->wherePivot('company_id', $companyId);
        } elseif ($this->current_company_id) {
            $query->wherePivot('company_id', $this->current_company_id);
        }

        return $query->exists();
    }

    public function hasAnyRole(array $roleNames, ?int $companyId = null): bool
    {
        foreach ($roleNames as $roleName) {
            if ($this->hasRole($roleName, $companyId)) {
                return true;
            }
        }
        return false;
    }

    public function assignRole(string $roleName, ?int $companyId = null)
    {
        $role = Role::where([
            'name' => $roleName,
            'guard_name' => 'web',
        ])->first();

        if (!$role) {
            $role = Role::create([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $companyId = $companyId ?? $this->current_company_id;

        // Check if already assigned
        $exists = DB::table('role_user')
            ->where('user_id', $this->id)
            ->where('role_id', $role->id)
            ->where('company_id', $companyId)
            ->exists();

        if (!$exists) {
            DB::table('role_user')->insert([
                'user_id' => $this->id,
                'role_id' => $role->id,
                'company_id' => $companyId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }


    // Permission check (stubbed for Phase 1)
    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('owner')) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    // Helper
    public function isOwnerOf(Company $company): bool
    {
        return $this->hasRole('owner', $company->id);
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function canAccessCompanySettings(): bool
    {
        return $this->isOwner();
    }

    public function canInviteStaff(): bool
    {
        return $this->isOwner();
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=3b82f6&color=fff&size=100';
    }
}
