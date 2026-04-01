<?php

namespace App\Repositories\Interfaces;

interface StockTakeRepositoryInterface
{
    public function paginate(int $perPage = 15);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
}