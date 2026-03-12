<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductService
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function getPaginatedProducts($perPage = 15)
    {
        return $this->productRepo->paginate($perPage, ['*'], ['category', 'brand']);
    }

    public function getProductById($id)
    {
        return $this->productRepo->findById($id, ['*'], ['category', 'brand', 'images']);
    }

    public function createProduct(array $data)
    {
        // Tại đây sau này bạn có thể gọi thêm ProductImageService để lưu ảnh
        return $this->productRepo->create($data);
    }

    public function updateProduct($id, array $data)
    {
        return $this->productRepo->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->productRepo->softDelete($id);
    }
}