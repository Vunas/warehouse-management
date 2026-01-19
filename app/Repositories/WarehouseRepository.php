<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Models\StorageBlock;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    protected $model;

    public function __construct(Warehouse $model)
    {
        $this->model = $model;
    }

    public function getAvailableBlocks()
    {
        return StorageBlock::where('status', 'available')
            ->with('warehouse')
            ->get();
    }

    public function getAllWithRelations()
    {
        return $this->model->with('type')->withCount('blocks')->get();
    }

    public function paginate($perPage = 10)
    {
        return $this->model->with('type')->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with(['blocks', 'type'])->findOrFail($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        $warehouse = $this->findById($id);
        $warehouse->update($data);
        return $warehouse;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getSelectable()
    {
        return $this->model
            ->select('id', 'name', 'type_id')
            ->with('type:id,type_code')
            ->get();
    }

    // --- Block Logic ---

    public function createBlock($data)
    {
        return StorageBlock::create($data);
    }

    public function findBlockById($id)
    {
        return StorageBlock::with('warehouse')->findOrFail($id);
    }

    public function updateBlock($id, $data)
    {
        $block = StorageBlock::findOrFail($id);
        $block->update($data);
        return $block;
    }
    public function sumTotalCapacity()
    {
        return Warehouse::sum('total_slots');
    }
    public function getRentableWarehousesWithAvailableBlocks()
    {
        return Warehouse::whereHas('type', function ($q) {
            $q->where('rentable', true);
        })
            ->whereHas('blocks', function ($q) {
                $q->where('status', 'available');
            })
            ->with([
                'type',
                'blocks' => function ($q) {
                    $q->where('status', 'available');
                }
            ])
            ->get();
    }
    public function findBlockForUpdate($id)
    {
        return StorageBlock::where('id', $id)->lockForUpdate()->firstOrFail();
    }
}
