<?php

namespace App\Repositories;

use App\Models\InboundTicket;
use App\Models\InboundDetail;
use App\Models\CalculatedSlot;
use App\Repositories\Interfaces\InboundTicketRepositoryInterface;

class InboundTicketRepository implements InboundTicketRepositoryInterface
{
    protected $model;

    public function __construct(InboundTicket $model)
    {
        $this->model = $model;
    }

    public function paginate($perPage = 15)
    {
        return $this->model->with(['contract.customer'])->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with([
            'details.product',
            'details.calculatedSlot',
            'contract.customer'
        ])->findOrFail($id);
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
        return InboundDetail::create($data);
    }

    public function createCalculatedSlot($data)
    {
        return CalculatedSlot::create($data);
    }

    public function updateStatus($id, $status)
    {
        $ticket = $this->findById($id);
        $ticket->update(['status' => $status]);

        if ($status === 'APPROVED' || $status === 'COMPLETED') {
            // Logic cập nhật thêm 
        }

        return $ticket;
    }
    public function countByStatus($status)
    {
        return InboundTicket::where('status', $status)->count();
    }

    public function getLatest($limit = 5)
    {
        return InboundTicket::with(['contract.customer.user'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
