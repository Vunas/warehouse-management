<?php

namespace App\Repositories;

use App\Models\ProductImage;
use App\Repositories\Interfaces\ProductImageRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class ProductImageRepository extends BaseRepository implements ProductImageRepositoryInterface
{
    // Lắp ráp: Có Read, Có Delete, Có Create (nhưng dùng Alias để ẩn Update)
    use CanRead;
    use CanWrite {
        create as traitCreate;
    }
    use CanDelete;

    public function getModel()
    {
        return ProductImage::class;
    }

    public function create(array $payload)
    {
        return $this->traitCreate($payload);
    }

    public function getByProductId(int $productId)
    {
        return $this->model->where('product_id', $productId)->get();
    }
}