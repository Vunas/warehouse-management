<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InternalTransfer;

class InternalTransferPolicy
{
    public function viewAny(User $user)
    {
        return $user->employee && $user->employee->hasPermission('inventory.view');
    }

    public function create(User $user)
    {
        return $user->employee && $user->employee->hasPermission('inventory.transfer');
    }

    // Quyền hoàn tất chuyển kho
    public function complete(User $user, InternalTransfer $transfer)
    {
        return $user->employee && $user->employee->hasPermission('inventory.transfer');
    }
}