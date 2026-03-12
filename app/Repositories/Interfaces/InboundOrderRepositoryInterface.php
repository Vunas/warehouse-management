<?php

namespace App\Repositories\Interfaces;

interface InboundOrderRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
    
    public function create(array $payload);
    public function update($id, array $payload); // Chủ yếu để update status
    public function delete($id); // Chỉ cho phép xóa khi status = 'pending'
}