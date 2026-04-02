<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class InventoryRepository implements InventoryRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    protected $model;

    public function __construct(Inventory $model)
    {
        $this->model = $model;
    }

    public function getPaginated(int $perPage = 15)
    {
        return $this->model->with(['product', 'location.warehouse'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    // Ghi đè phương thức của CanRead để luôn tự động gọi quan hệ cần thiết
    public function findById($id, array $columns = ['*'], array $relations = ['product', 'location.warehouse'])
    {
        return $this->model->with($relations)->findOrFail($id, $columns);
    }

    public function getStock(int $productId, int $locationId, ?int $batchId)
    {
        $query = $this->model->where('product_id', $productId)->where('location_id', $locationId);
        
        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $query->whereNull('batch_id');
        }

        return $query->first();
    }

    public function getLockedStock(int $productId, int $locationId, ?int $batchId)
    {
        $query = $this->model->where('product_id', $productId)->where('location_id', $locationId);
        
        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $query->whereNull('batch_id');
        }

        return $query->lockForUpdate()->first();
    }

    public function getAvailableStockByProduct(int $productId)
    {
        return $this->model->where('product_id', $productId)
            ->whereRaw('quantity > reserved_quantity')
            ->get();
    }

    public function getReservedStockByProduct(int $productId)
    {
        return $this->model->where('product_id', $productId)
            ->where('reserved_quantity', '>', 0)
            ->get();
    }

    public function getFefoStockByProductAndWarehouse(int $productId, int $warehouseId)
    {
        return $this->model->leftJoin('product_batches', 'inventory.batch_id', '=', 'product_batches.id')
            ->where('inventory.product_id', $productId)
            ->whereHas('location', function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->where('inventory.quantity', '>', 0)
            ->orderByRaw('ISNULL(product_batches.expiry_date), product_batches.expiry_date ASC')
            ->orderBy('inventory.created_at', 'ASC')
            ->select('inventory.*', 'product_batches.expiry_date')
            ->get();
    }

    public function getLockedById($id)
    {
        return $this->model->lockForUpdate()->find($id);
    }
}