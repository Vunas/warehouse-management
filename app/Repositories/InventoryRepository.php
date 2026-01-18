<?php

namespace App\Repositories;

use App\Models\InventoryItem;
use App\Models\InternalTransfer;
use App\Models\TransferItem;
use App\Models\InventoryTransaction;

class InventoryRepository
{
    // Inventory Items
    public function getItemsByWarehouse($warehouseId)
    {
        return InventoryItem::whereHas('storageBlock', function($q) use ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        })->with('product')->get();
    }

    public function findItemById($id)
    {
        return InventoryItem::findOrFail($id);
    }

    public function createItem(array $data)
    {
        return InventoryItem::create($data);
    }

    public function updateItem($id, array $data)
    {
        $item = $this->findItemById($id);
        $item->update($data);
        return $item;
    }

    public function deleteItem($id)
    {
        return InventoryItem::destroy($id);
    }

    // Internal Transfers
    public function getAllTransfersPaginated($perPage = 10)
    {
        return InternalTransfer::with(['fromBlock.warehouse', 'toBlock.warehouse'])->latest()->paginate($perPage);
    }

    public function createTransfer(array $data)
    {
        return InternalTransfer::create($data);
    }

    public function createTransferItem(array $data)
    {
        return TransferItem::create($data);
    }

    public function findTransferById($id)
    {
        return InternalTransfer::with('items.inventoryItem')->findOrFail($id);
    }

    public function updateTransferStatus($id, $status)
    {
        $transfer = $this->findTransferById($id);
        $transfer->update(['status' => $status]);
        return $transfer;
    }

    // Transactions Log
    public function logTransaction(array $data)
    {
        return InventoryTransaction::create($data);
    }
}