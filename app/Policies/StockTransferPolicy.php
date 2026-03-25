<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockTransfer;

class StockTransferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_transfers');
    }

    public function view(User $user, StockTransfer $stockTransfer): bool
    {
        return $user->hasPermissionTo('view_transfers');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_transfers');
    }

    public function update(User $user, StockTransfer $stockTransfer): bool
    {
        return $user->hasPermissionTo('edit_transfers') && $stockTransfer->status === 'pending';
    }

    public function delete(User $user, StockTransfer $stockTransfer): bool
    {
        return $user->hasPermissionTo('delete_transfers') && $stockTransfer->status === 'pending';
    }
}