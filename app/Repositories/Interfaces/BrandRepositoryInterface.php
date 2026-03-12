<?php

namespace App\Repositories\Interfaces;

interface BrandRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
    
    public function create(array $payload);
    public function update($id, array $payload);
    
    public function softDelete($id);
    public function restore($id);
    public function forceDelete($id);
}