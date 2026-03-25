<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;

class PaymentPolicy
{
    // Thường quyền xem/sửa thanh toán sẽ đi liền với quyền quản lý Đơn hàng
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view_orders'); }
    public function view(User $user, Payment $payment): bool { return $user->hasPermissionTo('view_orders'); }
    
    // Tuyệt đối không có function create() và delete() ở Admin cho Payment
    // Vì payment tự động sinh ra khi KH đặt hàng, và cấm xóa.

    public function update(User $user, Payment $payment): bool { return $user->hasPermissionTo('edit_orders'); }
}