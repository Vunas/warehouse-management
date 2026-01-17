<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;

class EmployeePolicy
{
    // Chỉ Admin hoặc Manager có quyền xem danh sách
    public function viewAny(User $user)
    {
        // Giả sử logic check role của bạn:
        // return $user->employee->hasRole('Admin') || $user->employee->hasRole('Manager');
        
        // Hoặc check permission (nếu dùng Spatie hoặc bảng Permissions tự build)
        return $user->employee->hasPermission('employee.view'); 
        }

    public function create(User $user)
    {
        return $user->employee->hasPermission('employee.create');
    }

    public function update(User $user)
    {
        return $user->employee->hasPermission('employee.update');
    }

    public function delete(User $user, Employee $employee)
    {
        // Không ai được xóa chính mình
        if ($user->id === $employee->user_id) {
            return false;
        }
        return $user->employee->hasPermission('employee.delete');
    }
}