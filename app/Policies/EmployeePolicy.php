<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;

class EmployeePolicy
{
    public function viewAny(User $user)
    {
        if (!$user->employee) {
            return false; // customer thì cấm
        }
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