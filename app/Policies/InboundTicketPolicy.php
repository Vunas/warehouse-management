<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InboundTicket;

class InboundTicketPolicy
{
    public function viewAny(User $user)
    {
        return $user->employee && $user->employee->hasPermission('inbound.view');
    }

    public function view(User $user, InboundTicket $ticket)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {
        return $user->employee && $user->employee->hasPermission('inbound.create');
    }

    // Quyền duyệt (Approve) & Tính slot
    public function approve(User $user, InboundTicket $ticket)
    {
        return $user->employee && $user->employee->hasPermission('inbound.approve');
    }

    // Quyền thực hiện nhập kho (Process)
    public function process(User $user, InboundTicket $ticket)
    {
        return $user->employee && $user->employee->hasPermission('inbound.process');
    }
}