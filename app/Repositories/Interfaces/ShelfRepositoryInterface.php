<?php

namespace App\Repositories\Interfaces;

interface ShelfRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByZoneId(int $zoneId);
    
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id);
}