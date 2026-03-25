<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    /**
     * Xem danh sách tất cả đơn hàng (Dành cho Admin/Nhân viên)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_orders');
    }

    /**
     * Xem chi tiết 1 đơn hàng
     * Có quyền xem tất cả HOẶC là chủ nhân của đơn hàng đó
     */
    public function view(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('view_orders') || $user->id === $order->user_id;
    }

    /**
     * Tạo đơn hàng (Bất kỳ user nào đăng nhập cũng được phép tạo)
     */
    public function create(User $user): bool
    {
        return true; 
    }

    /**
     * Cập nhật đơn hàng (Chỉ nhân viên có quyền mới được cập nhật trạng thái)
     */
    public function update(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('edit_orders');
    }

    /**
     * Hủy đơn hàng (Nhân viên có quyền HOẶC khách hàng tự hủy đơn đang ở trạng thái pending)
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('delete_orders') || 
               ($user->id === $order->user_id && $order->status === 'pending');
    }
}