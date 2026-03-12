<?php

namespace App\Repositories\Interfaces;

interface RoleRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
    
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id); // Xóa cứng vì không có deleted_at
}