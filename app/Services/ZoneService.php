<?php

namespace App\Services;

use App\Repositories\Interfaces\ZoneRepositoryInterface;

class ZoneService
{
    protected $zoneRepo;

    public function __construct(ZoneRepositoryInterface $zoneRepo)
    {
        $this->zoneRepo = $zoneRepo;
    }

    public function getZonesByWarehouse($warehouseId)
    {
        return $this->zoneRepo->getByWarehouseId($warehouseId);
    }

    public function createZone(array $data)
    {
        return $this->zoneRepo->create($data);
    }

    public function updateZone($id, array $data)
    {
        return $this->zoneRepo->update($id, $data);
    }

    public function deleteZone($id)
    {
        return $this->zoneRepo->delete($id);
    }
}