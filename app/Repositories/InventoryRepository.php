<?php

namespace App\Repositories;

use App\Models\InventoryItem;
use App\Models\InternalTransfer;
use App\Models\TransferItem;
use App\Models\InventoryTransaction;
use App\Repositories\Interfaces\InventoryRepositoryInterface;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function getItemsByWarehouse($warehouseId)
    {
        return InventoryItem::whereHas('storageBlock', function ($q) use ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        })->with(['product', 'storageBlock'])->get();
    }

    public function getFifoItemsForProduct($productId)
    {
        return InventoryItem::where('product_id', $productId)
            ->where('quantity_on_hand', '>', 0)
            ->orderBy('imported_at', 'asc')
            ->get();
    }

    public function findItemById($id)
    {
        return InventoryItem::findOrFail($id);
    }

    public function createItem($data)
    {
        return InventoryItem::create($data);
    }

    public function updateItem($id, $data)
    {
        $item = $this->findItemById($id);
        $item->update($data);
        return $item;
    }

    public function deleteItem($id)
    {
        return InventoryItem::destroy($id);
    }

    public function getTransfersPaginated($perPage = 10)
    {
        return InternalTransfer::with(['fromBlock.warehouse', 'toBlock.warehouse'])
            ->latest()
            ->paginate($perPage);
    }

    public function findTransferById($id)
    {
        return InternalTransfer::with(['items.inventoryItem.product', 'fromBlock', 'toBlock'])->findOrFail($id);
    }

    public function createTransfer($data)
    {
        return InternalTransfer::create($data);
    }

    public function createTransferItem($data)
    {
        return TransferItem::create($data);
    }

    public function updateTransferStatus($id, $status)
    {
        $transfer = InternalTransfer::findOrFail($id);
        $transfer->update(['status' => $status]);

        if ($status === 'COMPLETED') {
            $transfer->update(['completed_at' => now()]);
        }

        return $transfer;
    }

    public function logTransaction($data)
    {
        return InventoryTransaction::create($data);
    }
    public function searchInventory(array $filters, $perPage = 20)
    {
        $query = InventoryItem::with(['product', 'storageBlock.warehouse']);

        if (!empty($filters['warehouse_id'])) {
            $query->whereHas('storageBlock', function ($q) use ($filters) {
                $q->where('warehouse_id', $filters['warehouse_id']);
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function sumTotalUsedSlots()
    {
        return InventoryItem::sum('slot_used');
    }
}
