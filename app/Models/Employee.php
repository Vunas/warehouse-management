<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'position',
        'warehouse_id',
        'hired_at',
    ];

    protected $casts = [
        'hired_at' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'employee_role');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    // Helper to check permissions through roles
    public function hasPermission($permissionCode)
    {
        return $this->roles()->whereHas('permissions', function($q) use ($permissionCode) {
            $q->where('code', $permissionCode);
        })->exists();
    }
}