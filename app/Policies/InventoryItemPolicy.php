<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InventoryItem;

class InventoryItemPolicy
{
    public function viewAny(User $user)
    {
        return $user->employee && $user->employee->hasPermission('inventory.view');
    }

    public function view(User $user, InventoryItem $item)
    {
        return $this->viewAny($user);
    }

    // Không có create/update/delete trực tiếp cho InventoryItem
    // Vì InventoryItem chỉ được sinh ra từ quy trình Inbound/Outbound/Transfer
}