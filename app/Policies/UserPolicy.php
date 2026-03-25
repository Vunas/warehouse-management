<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Ai có quyền xem danh sách người dùng?
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_users');
    }

    /**
     * Ai có quyền xem chi tiết 1 người dùng?
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('view_users') || $user->id === $model->id;
    }

    /**
     * Ai có quyền tạo người dùng mới?
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_users');
    }

    /**
     * Ai có quyền cập nhật người dùng?
     */
    public function update(User $user, User $model): bool
    {
        // Có quyền sửa user HOẶC là tự sửa profile của chính mình
        return $user->hasPermissionTo('edit_users') || $user->id === $model->id;
    }

    /**
     * Ai có quyền xóa người dùng?
     */
    public function delete(User $user, User $model): bool
    {
        // Có quyền xóa VÀ không được phép tự xóa chính mình
        return $user->hasPermissionTo('delete_users') && $user->id !== $model->id;
    }
}