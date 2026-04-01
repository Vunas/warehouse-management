<?php

namespace App\Repositories;

use App\Models\StockTransfer;
use App\Repositories\Interfaces\StockTransferRepositoryInterface;
use App\Repositories\Traits\CanRead;
use App\Repositories\Traits\CanWrite;
use App\Repositories\Traits\CanDelete;

class StockTransferRepository implements StockTransferRepositoryInterface
{
    use CanRead, CanWrite, CanDelete;

    protected $model;

    public function __construct(StockTransfer $model)
    {
        $this->model = $model;
    }

    public function filterAndPaginate(array $filters, int $perPage = 15)
    {
        $query = $this->model->with(['staff', 'fromWarehouse', 'toWarehouse'])
                             ->orderBy('id', 'desc');

        if (!empty($filters['search'])) {
            $searchId = str_replace(['TRF-', 'trf-'], '', $filters['search']);
            $query->where('id', (int)$searchId);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }
}