<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;

class WarehousePolicy
{
    public function viewAny(User $user)
    {
        return $user->employee && $user->employee->hasPermission('warehouse.view');
    }

    public function view(User $user, Warehouse $warehouse)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {
        return $user->employee && $user->employee->hasPermission('warehouse.create');
    }

    public function update(User $user, Warehouse $warehouse)
    {
        return $user->employee && $user->employee->hasPermission('warehouse.update');
    }

    public function delete(User $user, Warehouse $warehouse)
    {
        // Thường chỉ Admin mới được xóa kho
        return $user->employee && $user->employee->hasPermission('warehouse.update'); 
    }
}