<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanSoftDelete;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    use CanRead, CanWrite, CanSoftDelete;

    public function getModel()
    {
        return Order::class;
    }
}