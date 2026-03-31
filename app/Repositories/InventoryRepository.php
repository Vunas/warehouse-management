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
        return $this->model->with('location.warehouse')
            ->where('product_id', $productId)
            ->get();
    }

    public function getByLocationId(int $locationId)
    {
        return $this->model->with('product')
            ->where('location_id', $locationId)
            ->get();
    }

    public function findByProductAndLocation(int $productId, int $locationId)
    {
        return $this->model->where('product_id', $productId)
            ->where('location_id', $locationId)
            ->first();
    }

    // Lấy các lô hàng có "tồn khả dụng" (Số lượng - Đã giữ > 0) để xuất bán mới hoặc xuất nội bộ
    public function getAvailableStockByProduct(int $productId)
    {
        return $this->model->where('product_id', $productId)
            ->whereRaw('(quantity - reserved_quantity) > 0')
            ->orderBy('created_at', 'asc') // FIFO
            ->get();
    }

    // Lấy các lô hàng đang được "giữ chỗ" (Dành cho việc xuất kho sau khi đã reserve)
    public function getReservedStockByProduct(int $productId)
    {
        return $this->model->where('product_id', $productId)
            ->where('reserved_quantity', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO
            ->get();
    }

    public function findByProductLocationBatch($productId, $locationId, $batchId)
    {
        return Inventory::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->where('batch_id', $batchId)
            ->first();
    }
}
