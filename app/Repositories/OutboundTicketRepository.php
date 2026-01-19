<?php

namespace App\Repositories;

use App\Models\OutboundTicket;
use App\Models\OutboundDetail;
use App\Repositories\Interfaces\OutboundTicketRepositoryInterface;

class OutboundTicketRepository implements OutboundTicketRepositoryInterface
{
    protected $model;

    public function __construct(OutboundTicket $model)
    {
        $this->model = $model;
    }

    public function paginate($perPage = 15)
    {
        return $this->model->with(['contract.customer'])->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with(['details.product', 'contract'])->findOrFail($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        $ticket = $this->findById($id);
        $ticket->update($data);
        return $ticket;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function createDetail($data)
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