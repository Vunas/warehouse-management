<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InboundOrder;

class InboundOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_inbounds');
    }

    public function view(User $user, InboundOrder $inboundOrder): bool
    {
        return $user->hasPermissionTo('view_inbounds');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_inbounds');
    }

    /**
     * Chỉ được sửa khi phiếu nhập kho đang ở trạng thái pending
     */
    public function update(User $user, InboundOrder $inboundOrder): bool
    {
        return $user->hasPermissionTo('edit_inbounds') && $inboundOrder->status === 'pending';
    }

    /**
     * Chỉ được hủy khi phiếu nhập chưa hoàn tất
     */
    public function delete(User $user, InboundOrder $inboundOrder): bool
    {
        return $user->hasPermissionTo('delete_inbounds') && $inboundOrder->status === 'pending';
    }
}