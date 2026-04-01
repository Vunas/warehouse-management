<?php

namespace App\Repositories\Interfaces;

interface InventoryRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
    public function getPaginated(int $perPage = 15);
    public function findById($id, array $columns = ['*'], array $relations = ['product', 'location.warehouse']);
    public function getStock(int $productId, int $locationId, ?int $batchId);
    public function getLockedStock(int $productId, int $locationId, ?int $batchId);
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id);
    public function getAvailableStockByProduct(int $productId);
    public function getReservedStockByProduct(int $productId);
    public function getFefoStockByProductAndWarehouse(int $productId, int $warehouseId);
    public function getLockedById($id);

}