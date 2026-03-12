<?php

namespace App\Repositories;

use App\Models\Zone;
use App\Repositories\Interfaces\ZoneRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class ZoneRepository extends BaseRepository implements ZoneRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return Zone::class;
    }

    public function getByWarehouseId(int $warehouseId)
    {
        return $this->model->where('warehouse_id', $warehouseId)->get();
    }
}