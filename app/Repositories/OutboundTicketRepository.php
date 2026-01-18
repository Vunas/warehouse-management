<?php

namespace App\Repositories;

use App\Models\OutboundTicket;
use App\Models\OutboundDetail;

class OutboundTicketRepository
{
    public function getAllPaginated($perPage = 15)
    {
        return OutboundTicket::with(['contract.customer.user'])->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return OutboundTicket::with(['details.product', 'contract'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return OutboundTicket::create($data);
    }

    public function createDetail(array $data)
    {
        return OutboundDetail::create($data);
    }

    public function updateStatus($id, $status)
    {
        $ticket = $this->findById($id);
        $ticket->update(['status' => $status]);
        return $ticket;
    }
}