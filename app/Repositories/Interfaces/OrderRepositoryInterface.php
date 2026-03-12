<?php

namespace App\Repositories\Interfaces;

interface OrderRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
    
    public function create(array $payload);
    public function update($id, array $payload); // Update status, address
    
    public function softDelete($id);
    public function restore($id);
    public function forceDelete($id);
}