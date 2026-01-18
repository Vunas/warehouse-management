<?php

namespace App\Repositories;

use App\Models\InboundTicket;
use App\Models\InboundDetail;
use App\Models\CalculatedSlot;

class InboundTicketRepository
{
    public function getAllPaginated($perPage = 15)
    {
        return InboundTicket::with(['contract.customer.user'])->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return InboundTicket::with(['details.product', 'details.calculatedSlot', 'contract'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return InboundTicket::create($data);
    }

    public function createDetail(array $data)
    {
        return InboundDetail::create($data);
    }

    public function createCalculatedSlot(array $data)
    {
        return CalculatedSlot::create($data);
    }

    public function updateStatus($id, $status)
    {
        $ticket = $this->findById($id);
        $ticket->update(['status' => $status]);
        return $ticket;
    }
}