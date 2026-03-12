<?php

namespace App\Repositories\Interfaces;

interface InventoryRepositoryInterface
{
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
    
    public function getByProductId(int $productId);
    public function getByShelfId(int $shelfId);
    
    public function create(array $payload);
    public function update($id, array $payload); // Chỉ update số lượng quantity
    
    // KHÔNG CÓ delete(). Hàng hết thì quantity = 0, xóa mất dữ liệu lịch sử tồn.
}