<?php

namespace App\Repositories;

use App\Models\InventoryTransaction;
use App\Repositories\Interfaces\InventoryTransactionRepositoryInterface;

class InventoryTransactionRepository implements InventoryTransactionRepositoryInterface
{
    public function filterAndPaginate(array $filters, int $perPage = 20)
    {
        $query = InventoryTransaction::with([
            'product', 'location.warehouse', 'batch', 'staff'
        ])->latest('id');

        if (!empty($filters['type'])) {
            $query->where('transaction_type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        return $query->paginate($perPage);
    }

    public function create(array $data)
    {
        return InventoryTransaction::create($data);
    }
}