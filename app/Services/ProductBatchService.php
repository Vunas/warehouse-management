<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductBatchRepositoryInterface;
use Illuminate\Support\Str;

class ProductBatchService
{
    protected $batchRepo;

    public function __construct(ProductBatchRepositoryInterface $batchRepo)
    {
        $this->batchRepo = $batchRepo;
    }

    public function getAllPaginated(int $perPage = 15)
    {
        return $this->batchRepo->paginate($perPage);
    }

    public function createBatch(array $data)
    {
        // Tự động sinh mã lô nếu bỏ trống
        if (empty($data['batch_code'])) {
            $data['batch_code'] = 'BATCH-' . strtoupper(Str::random(6)) . '-' . date('ym');
        }

        return $this->batchRepo->create($data);
    }

    public function getBatchById(int $id)
    {
        return $this->batchRepo->findById($id);
    }

    public function updateBatch(int $id, array $data)
    {
        return $this->batchRepo->update($id, $data);
    }

    public function deleteBatch(int $id)
    {
        return $this->batchRepo->delete($id);
    }
}