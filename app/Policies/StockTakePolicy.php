<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockTake;

class StockTakePolicy
{
    /**
     * Xem danh sách phiếu kiểm kê
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_stock_takes');
    }

    /**
     * Xem chi tiết một phiếu kiểm kê
     */
    public function view(User $user, StockTake $stockTake): bool
    {
        return $user->hasPermissionTo('view_stock_takes');
    }

    /**
     * Tạo phiếu kiểm kê mới
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_stock_takes');
    }

    /**
     * Cập nhật phiếu kiểm kê (chỉnh sửa số lượng đếm, trạng thái...)
     */
    public function update(User $user, StockTake $stockTake): bool
    {
        return $user->hasPermissionTo('edit_stock_takes');
    }

    /**
     * Xóa/Hủy phiếu kiểm kê
     */
    public function delete(User $user, StockTake $stockTake): bool
    {
        return $user->hasPermissionTo('delete_stock_takes');
    }
}