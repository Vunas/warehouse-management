<?php

namespace App\Repositories\Interfaces;

interface InventoryRepositoryInterface
{
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);

    public function getByProductId(int $productId);
    public function getByLocationID(int $shelfId);

    public function findByProductAndLocation(int $productId, int $shelfId);
    public function getAvailableStockByProduct(int $productId);

    public function create(array $payload);
    public function update($id, array $payload);
    public function getReservedStockByProduct(int $productId);
    public function findByProductLocationBatch($productId, $locationId, $batchId);


    // KHÔNG CÓ delete(). Hàng hết thì quantity = 0, xóa mất dữ liệu lịch sử tồn.
}
