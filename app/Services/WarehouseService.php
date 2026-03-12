<?php

namespace App\Services;

use App\Repositories\Interfaces\WarehouseRepositoryInterface;

class WarehouseService
{
    protected $warehouseRepo;

    public function __construct(WarehouseRepositoryInterface $warehouseRepo)
    {
        $this->warehouseRepo = $warehouseRepo;
    }

    public function getPaginatedWarehouses($perPage = 15)
    {
        return $this->warehouseRepo->paginate($perPage);
    }

    public function createWarehouse(array $data)
    {
        return $this->warehouseRepo->create($data);
    }

    public function updateWarehouse($id, array $data)
    {
        return $this->warehouseRepo->update($id, $data);
    }

    public function deleteWarehouse($id)
    {
        return $this->warehouseRepo->softDelete($id);
    }
}