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

    public function getPaginated(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->with(['product', 'location.warehouse', 'batch']);

        // --- 1. XỬ LÝ LỌC DỮ LIỆU (FILTERING) ---

        // Tìm theo tên/mã SP
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->whereHas('product', function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('id', $keyword);
            });
        }

        // Lọc theo Kho
        if (!empty($filters['warehouse_id'])) {
            $warehouseId = $filters['warehouse_id'];
            $query->whereHas('location', function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            });
        }

        // Lọc theo Trạng thái
        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $query->where('quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'available':
                    $query->whereRaw('quantity > reserved_quantity');
                    break;
                case 'reserved':
                    $query->where('reserved_quantity', '>', 0);
                    break;
            }
        }

        // Lọc theo Lô hàng
        if (!empty($filters['batch_code'])) {
            $batchCode = $filters['batch_code'];
            $query->whereHas('batch', function ($q) use ($batchCode) {
                $q->where('batch_code', 'LIKE', "%{$batchCode}%");
            });
        }

        // --- 2. XỬ LÝ SẮP XẾP (SORTING) ---

        $sortColumn = $filters['sort'] ?? 'updated_at'; // Mặc định xếp theo ngày cập nhật
        $sortDirection = strtolower($filters['dir'] ?? 'desc'); // Mặc định giảm dần

        // Danh sách các cột ĐƯỢC PHÉP sắp xếp (Bảo mật: Tránh SQL Injection)
        $allowedSortColumns = ['id', 'quantity', 'reserved_quantity', 'updated_at', 'created_at'];

        // Đảm bảo direction chỉ là asc hoặc desc
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        if (in_array($sortColumn, $allowedSortColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            // Sắp xếp mặc định nếu param sai
            $query->orderBy('updated_at', 'desc');
        }

        return $query->paginate($perPage);
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
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    public function getReservedStockByProduct(int $productId)
    {
        return $this->model->where('product_id', $productId)
            ->where('reserved_quantity', '>', 0)
            ->orderBy('created_at', 'ASC') // FIFO
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
