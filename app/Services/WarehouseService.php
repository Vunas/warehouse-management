<?php

namespace App\Services;

use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class WarehouseService
{
    protected $warehouseRepo;

    public function __construct(WarehouseRepositoryInterface $warehouseRepo)
    {
        $this->warehouseRepo = $warehouseRepo;
    }

    public function getAllWarehouses()
    {
        return $this->warehouseRepo->getAllWithRelations();
    }

    public function getAvailableBlocks()
    {
        return $this->warehouseRepo->getAvailableBlocks();
    }

    public function getWarehouseSelection()
    {
        return $this->warehouseRepo->getSelectable();
    }

    public function createWarehouseWithBlocks(array $data)
    {
        DB::beginTransaction();
        try {
            $warehouse = $this->warehouseRepo->create([
                'name' => $data['name'],
                'type_id' => $data['type_id'],
                'warehouse_code' => $data['warehouse_code'] ?? 'WH' . time(),
                'total_capacity_slots' => 0,
                'status' => 'ACTIVE',
            ]);

            $slotsPerBlock = $data['slots_per_block'];
            $totalSlots = 0;

            for ($i = 1; $i <= $data['total_blocks']; $i++) {
                $this->warehouseRepo->createBlock([
                    'warehouse_id' => $warehouse->id,
                    'block_code' => 'BLK-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'total_slots' => $slotsPerBlock,
                    'status' => 'AVAILABLE'
                ]);
                $totalSlots += $slotsPerBlock;
            }

            $this->warehouseRepo->update($warehouse->id, ['total_capacity_slots' => $totalSlots]);

            DB::commit();
            return $warehouse;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function getTotalCapacity()
    {
        return $this->warehouseRepo->sumTotalCapacity();
    }

    public function updateWarehouse($id, array $data)
    {
        return $this->warehouseRepo->update($id, $data);
    }

    public function getRentableWarehousesWithAvailableBlocks()
    {
        return $this->warehouseRepo->getRentableWarehousesWithAvailableBlocks();
    }

    public function deleteWarehouse($id)
    {
        return $this->warehouseRepo->delete($id);
    }
}
