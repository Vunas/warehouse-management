<?php

namespace App\Repositories;

use App\Models\Shelf;
use App\Repositories\Interfaces\ShelfRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class ShelfRepository extends BaseRepository implements ShelfRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    public function getModel()
    {
        return Shelf::class;
    }

    public function getByZoneId(int $zoneId)
    {
        return $this->model->where('zone_id', $zoneId)->get();
    }
}