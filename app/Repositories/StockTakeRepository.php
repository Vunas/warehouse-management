<?php

namespace App\Repositories;

use App\Models\StockTake;
use App\Repositories\Interfaces\StockTakeRepositoryInterface;

class StockTakeRepository extends BaseRepository implements StockTakeRepositoryInterface
{
    public function getModel()
    {
        return StockTake::class;
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->with(['warehouse', 'staff'])->latest('id')->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->model->with([
            'warehouse', 
            'staff',
            'items.product',
            'items.location',
            'items.batch'
        ])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);
        return $record;
    }
}