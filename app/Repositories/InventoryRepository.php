<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    use CanRead;
    use CanWrite {
        create as traitCreate;
        update as traitUpdate;
    }

    public function getModel()
    {
        return Inventory::class;
    }

    public function create(array $payload)
    {
        return $this->traitCreate($payload);
    }

    public function update($id, array $payload)
    {
        return $this->traitUpdate($id, $payload);
    }

    public function getByProductId(int $productId)
    {
        return $this->model->with('shelf.zone.warehouse')->where('product_id', $productId)->get();
    }

    public function getByShelfId(int $shelfId)
    {
        return $this->model->with('product')->where('shelf_id', $shelfId)->get();
    }
}