<?php

namespace App\Repositories;

use App\Models\InventoryTransaction;
use App\Repositories\Interfaces\InventoryTransactionRepositoryInterface;

class InventoryTransactionRepository implements InventoryTransactionRepositoryInterface
{
    public function filterAndPaginate(array $filters, int $perPage = 20)
    {
        // Eager load các relations để tránh lỗi N+1 query
        $query = InventoryTransaction::with(['product', 'location.warehouse', 'batch', 'staff']);

        // 1. Lọc theo loại giao dịch
        if (!empty($filters['type'])) {
            $query->where('transaction_type', $filters['type']);
        }

        // 2. Lọc theo khoảng thời gian
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // 3. Lọc theo thông tin Sản phẩm (Tên, Mã SP, Khoảng giá)
        // Gom chung vào 1 whereHas để tối ưu query
        if (!empty($filters['search']) || isset($filters['price_from']) || isset($filters['price_to'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                // Lọc theo tên hoặc ID sản phẩm
                if (!empty($filters['search'])) {
                    $search = $filters['search'];
                    $q->where(function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%")
                            ->orWhere('id', $search);
                    });
                }

                // Lọc theo khoảng giá của sản phẩm
                if (isset($filters['price_from']) && $filters['price_from'] !== '') {
                    $q->where('price', '>=', $filters['price_from']);
                }
                if (isset($filters['price_to']) && $filters['price_to'] !== '') {
                    $q->where('price', '<=', $filters['price_to']);
                }
            });
        }

        // Sắp xếp mới nhất lên đầu
        return $query->latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return InventoryTransaction::create($data);
    }
}
