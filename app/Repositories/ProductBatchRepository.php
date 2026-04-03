<?php

namespace App\Repositories;

use App\Models\ProductBatch;
use App\Repositories\Interfaces\ProductBatchRepositoryInterface;

class ProductBatchRepository implements ProductBatchRepositoryInterface
{
    public function paginate(int $perPage = 15)
    {
        return ProductBatch::with('product')->latest()->paginate($perPage);
    }

    public function findById(int $id)
    {
        return ProductBatch::findOrFail($id);
    }

    public function create(array $data)
    {
        return ProductBatch::create($data);
    }

    public function update(int $id, array $data)
    {
        $batch = $this->findById($id);
        $batch->update($data);
        return $batch;
    }

    public function delete(int $id)
    {
        $batch = $this->findById($id);
        return $batch->delete();
    }
}