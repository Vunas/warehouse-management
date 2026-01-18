<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy
{
    // Admin, Manager, Staff đều được xem danh sách KH để làm Hợp đồng/Xuất nhập
    public function viewAny(User $user)
    {
        return $user->employee !== null; 
    }

    // Chỉ Admin và Manager được tạo/sửa khách hàng
    public function create(User $user)
    {
        return $user->employee->hasPermission('contract.create');
    }

    public function update(User $user)
    {
        return $user->employee->hasPermission('contract.update');
    }

    public function delete(User $user)
    {
        return $user->employee->hasPermission('contract.delete');
    }
}