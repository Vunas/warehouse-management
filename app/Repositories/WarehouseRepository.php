<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Models\StorageBlock;

class WarehouseRepository
{
    public function getAllWithRelations()
    {
        return Warehouse::with('type')->withCount('blocks')->get();
    }

    public function findById($id)
    {
        return Warehouse::with(['blocks', 'type'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Warehouse::create($data);
    }

    public function update($id, array $data)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($data);
        return $warehouse;
    }

    public function delete($id)
    {
        return Warehouse::destroy($id);
    }

    // Xử lý Block
    public function createBlock(array $data)
    {
        return StorageBlock::create($data);
    }

    public function findBlockById($id)
    {
        return StorageBlock::findOrFail($id);
    }
}