<?php

namespace App\Repositories;

use App\Models\OutboundOrder;
use App\Repositories\Interfaces\OutboundOrderRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class OutboundOrderRepository implements OutboundOrderRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    protected $model;

    public function __construct(OutboundOrder $model)
    {
        $this->model = $model;
    }

    public function getPaginatedOrders(int $perPage = 15)
    {
        return $this->model->with(['order', 'staff', 'warehouse'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }
}