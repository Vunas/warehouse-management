<?php

namespace App\Repositories\Interfaces;

interface ZoneRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByWarehouseId(int $warehouseId);
    
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id);
}