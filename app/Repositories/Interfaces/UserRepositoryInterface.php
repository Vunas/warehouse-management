<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    // Đọc
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
    
    // Ghi
    public function create(array $payload);
    public function update($id, array $payload);
    
    // Xóa mềm (Do DB có deleted_at)
    public function softDelete($id);
    public function restore($id);
    public function forceDelete($id);
}