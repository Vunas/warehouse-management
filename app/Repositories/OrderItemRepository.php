<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    use CanRead;
    use CanWrite {
        create as traitCreate;
    }
    use CanDelete;

    public function getModel()
    {
        return OrderItem::class;
    }

    public function create(array $payload)
    {
        return $this->traitCreate($payload);
    }

    public function getByOrderId(int $orderId)
    {
        return $this->model->with('product')->where('order_id', $orderId)->get();
    }
}