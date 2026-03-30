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
        return $user->hasPermissionTo('view_orders') || $user->id;
    }

    /**
     * Xem chi tiết 1 đơn hàng
     * Có quyền xem tất cả HOẶC là chủ nhân của đơn hàng đó
     */
    public function view(User $user, Order $order): bool
    {
        // Check if user is the order owner (works for customers)
        if ($user->id === $order->user_id) {
            return true;
        }
        
        // Check permission for admin/staff (only for web guard)
        try {
            return $user->hasPermissionTo('view_orders');
        } catch (\Exception $e) {
            return false;
        }
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
        try {
            return $user->hasPermissionTo('edit_orders');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Hủy đơn hàng (Nhân viên có quyền HOẶC khách hàng tự hủy đơn đang ở trạng thái pending)
     */
    public function delete(User $user, Order $order): bool
    {
        // Customers can cancel their own pending orders
        if ($user->id === $order->user_id && $order->status === 'pending') {
            return true;
        }
        
        // Check admin permission
        try {
            return $user->hasPermissionTo('delete_orders');
        } catch (\Exception $e) {
            return false;
        }
    }
}