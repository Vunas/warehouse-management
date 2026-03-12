<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'email',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];


    protected $casts = [
        'is_active' => 'boolean', 
        'password'  => 'hashed',  
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Quan hệ n-n với bảng roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS (Dành cho RBAC)
    |--------------------------------------------------------------------------
    */

    /**
     * Kiểm tra User có role cụ thể không
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('role_name', $roleName);
    }

    /**
     * Kiểm tra User có permission cụ thể không (Xuyên qua Role)
     */
    public function hasPermission(string $permissionCode): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('permission_code', $permissionCode)) {
                return true;
            }
        }
        return false;
    }
}