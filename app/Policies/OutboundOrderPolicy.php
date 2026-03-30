<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OutboundOrder;

class OutboundOrderPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('view_outbounds'); }
    public function view(User $user, OutboundOrder $outbound): bool { return $user->hasPermissionTo('view_outbounds'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_outbounds'); }
    public function update(User $user, OutboundOrder $outbound): bool { return $user->hasPermissionTo('edit_outbounds'); }
    public function delete(User $user, OutboundOrder $outbound): bool { return $user->hasPermissionTo('delete_outbounds'); }
}