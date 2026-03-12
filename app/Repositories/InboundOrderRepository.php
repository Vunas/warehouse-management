<?php

namespace App\Repositories;

use App\Models\InboundOrder;
use App\Repositories\Interfaces\InboundOrderRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class InboundOrderRepository extends BaseRepository implements InboundOrderRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return InboundOrder::class;
    }
}