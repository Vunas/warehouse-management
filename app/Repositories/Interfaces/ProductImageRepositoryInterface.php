<?php

namespace App\Repositories\Interfaces;

interface ProductImageRepositoryInterface
{
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByProductId(int $productId);
    
    public function create(array $payload);
    public function delete($id);
    
    // KHÔNG CÓ update(). Ảnh sai thì xóa và thêm mới.
}