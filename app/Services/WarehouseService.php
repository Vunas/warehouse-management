<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Models\StorageBlock;
use Illuminate\Support\Facades\DB;
use Exception;

class WarehouseService
{
    public function createWarehouseWithBlocks(array $data)
    {
        DB::beginTransaction();
        try {
            // 1. Tạo Kho
            $warehouse = Warehouse::create([
                'name' => $data['name'],
                'type_id' => $data['type_id'],
                'total_blocks' => $data['total_blocks'],
                // Total slots sẽ được cập nhật sau
                'status' => 'active',
            ]);

            // 2. Tạo nhanh các Storage Block (Lô)
            $slotsPerBlock = $data['slots_per_block'];
            $totalSlots = 0;

            for ($i = 1; $i <= $data['total_blocks']; $i++) {
                $blockCode = $this->generateBlockCode($i); 

                StorageBlock::create([
                    'warehouse_id' => $warehouse->id,
                    'block_code' => $blockCode,
                    'total_slots' => $slotsPerBlock,
                    'status' => 'available'
                ]);

                $totalSlots += $slotsPerBlock;
            }

            // 3. Cập nhật lại tổng slot cho Warehouse
            $warehouse->update(['total_slots' => $totalSlots]);

            DB::commit();
            return $warehouse;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateWarehouse($id, array $data)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($data);
        return $warehouse;
    }

    public function deleteWarehouse($id)
    {
        return Warehouse::destroy($id);
    }

    private function generateBlockCode($index)
    {
        return 'BLK-' . str_pad($index, 2, '0', STR_PAD_LEFT);
    }
}
