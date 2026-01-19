<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy
{
    public function viewAny(User $user)
    {
        // Ai có quyền xem customer hoặc làm hợp đồng đều xem được
        return $user->employee && ($user->employee->hasPermission('customer.view') || $user->employee->hasPermission('contract.create'));
    }

    public function view(User $user, Customer $customer)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {
        return $user->employee && $user->employee->hasPermission('customer.create');
    }

    public function update(User $user, Customer $customer)
    {
        return $user->employee && $user->employee->hasPermission('customer.update');
    }

    public function delete(User $user, Customer $customer)
    {
        return $user->employee && $user->employee->hasPermission('customer.delete');
    }
}
