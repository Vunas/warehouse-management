<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Inventory;

class InventoryPolicy
{
    /**
     * Xem danh sách tồn kho
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_inventory');
    }

    /**
     * Xem chi tiết tồn kho
     */
    public function view(User $user, Inventory $inventory): bool
    {
        return $user->hasPermissionTo('view_inventory');
    }

    /**
     * Tạo tồn kho (thường ít dùng)
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_inventory');
    }

    /**
     * Cập nhật tồn kho (ví dụ chỉnh số lượng, điều chỉnh)
     */
    public function update(User $user, Inventory $inventory): bool
    {
        return $user->hasPermissionTo('edit_inventory');
    }

    /**
     * Xóa tồn kho (nếu cho phép)
     */
    public function delete(User $user, Inventory $inventory): bool
    {
        return $user->hasPermissionTo('delete_inventory');
    }
}
