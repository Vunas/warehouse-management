<?php

namespace App\Repositories;

use App\Models\InboundItem;
use App\Repositories\Interfaces\InboundItemRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class InboundItemRepository implements InboundItemRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    protected $model;

    public function __construct(InboundItem $model)
    {
        $this->model = $model;
    }

    public function getByInboundId(int $inboundId)
    {
        return $this->model->with('product')->where('inbound_id', $inboundId)->get();
    }

    public function findByInboundAndProduct(int $inboundId, int $productId)
    {
        return $this->model->where('inbound_id', $inboundId)
                           ->where('product_id', $productId)
                           ->first();
    }
}