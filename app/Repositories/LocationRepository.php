<?php

namespace App\Repositories;

use App\Models\Location;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class LocationRepository extends BaseRepository implements LocationRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return Location::class;
    }

    public function getByWarehouse(int $warehouseId)
    {
        return $this->model->where('warehouse_id', $warehouseId)->get();
    }

    public function getChildren(int $parentId)
    {
        return $this->model->where('parent_id', $parentId)->get();
    }

    public function getRootLocations(int $warehouseId)
    {
        return $this->model->where('warehouse_id', $warehouseId)
                           ->whereNull('parent_id')
                           ->get();
    }
}
