<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Contract;

class ContractPolicy
{
    public function viewAny(User $user)
    {
        return $user->employee && $user->employee->hasPermission('contract.view');
    }

    public function view(User $user, Contract $contract)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {
        return $user->employee && $user->employee->hasPermission('contract.create');
    }

    public function update(User $user, Contract $contract)
    {
        return $user->employee && $user->employee->hasPermission('contract.update');
    }

    public function delete(User $user, Contract $contract)
    {
        return $user->employee && $user->employee->hasPermission('contract.delete');
    }
}