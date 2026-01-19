<?php

namespace App\Repositories\Interfaces;

interface InventoryRepositoryInterface
{
    public function getItemsByWarehouse($warehouseId);
    public function getFifoItemsForProduct($productId);
    public function findItemById($id);
    public function createItem($data);
    public function updateItem($id, $data);
    public function deleteItem($id);

    public function getTransfersPaginated($perPage = 10);
    public function findTransferById($id);
    public function createTransfer($data);
    public function createTransferItem($data);
    public function updateTransferStatus($id, $status);

    public function logTransaction($data);
    public function searchInventory(array $filters, $perPage = 20);

    public function sumTotalUsedSlots();
}
