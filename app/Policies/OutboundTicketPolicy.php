<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OutboundTicket;

class OutboundTicketPolicy
{
    public function viewAny(User $user)
    {
        return $user->employee && $user->employee->hasPermission('outbound.view');
    }

    public function view(User $user, OutboundTicket $ticket)
    {
        return $this->viewAny($user);
    }

    public function create(User $user)
    {
        return $user->employee && $user->employee->hasPermission('outbound.create');
    }

    public function process(User $user, OutboundTicket $ticket)
    {
        return $user->employee && $user->employee->hasPermission('outbound.process');
    }
}