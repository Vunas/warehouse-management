<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy
{
    // Admin, Manager, Staff đều được xem danh sách KH để làm Hợp đồng/Xuất nhập
    public function viewAny(User $user)
    {
        // Check permission thay vì Role cứng
        // Giả sử permission code là 'contract.view' hoặc 'customer.view'
        // Ở đây mình dùng logic đơn giản: nhân viên nào cũng xem được KH
        return $user->employee !== null; 
    }

    // Chỉ Admin và Manager được tạo/sửa khách hàng
    public function create(User $user)
    {
        return $user->employee->hasPermission('contract.create'); // Thường người tạo HĐ sẽ tạo KH
    }

    public function update(User $user, Customer $customer)
    {
        return $user->employee->hasPermission('contract.update');
    }

    public function delete(User $user, Customer $customer)
    {
        return $user->employee->hasPermission('contract.delete');
    }
}