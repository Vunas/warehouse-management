<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InventoryTransaction;

class InventoryTransactionPolicy
{
    /**
     * Xem danh sách lịch sử giao dịch kho
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_inventory_transactions');
    }

    /**
     * Xem chi tiết một giao dịch kho
     */
    public function view(User $user, InventoryTransaction $transaction): bool
    {
        return $user->hasPermissionTo('view_inventory_transactions');
    }

    /**
     * Tạo giao dịch kho (thường hệ thống tự tạo, nhưng nếu cần chức năng tạo tay)
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_inventory_transactions');
    }

    /**
     * Cập nhật giao dịch kho (Rất hạn chế vì đây là log)
     */
    public function update(User $user, InventoryTransaction $transaction): bool
    {
        return $user->hasPermissionTo('edit_inventory_transactions');
    }

    /**
     * Xóa giao dịch kho (Tuyệt đối hạn chế)
     */
    public function delete(User $user, InventoryTransaction $transaction): bool
    {
        return $user->hasPermissionTo('delete_inventory_transactions');
    }
}