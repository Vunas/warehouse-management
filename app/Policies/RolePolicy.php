<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    // Giả định chúng ta sẽ thêm quyền 'manage_roles' cho Admin
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    public function update(User $user, Role $role): bool
    {
        // Không cho phép sửa tên vai trò 'admin' để tránh lỗi hệ thống
        if ($role->name === 'admin') {
            return false;
        }
        return $user->hasPermissionTo('manage_roles');
    }

    public function delete(User $user, Role $role): bool
    {
        // Tuyệt đối không cho phép xóa vai trò 'admin'
        if ($role->name === 'admin') {
            return false;
        }
        return $user->hasPermissionTo('manage_roles');
    }
}

