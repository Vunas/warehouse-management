<?php

namespace App\Repositories\Interfaces;

interface AddressRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByUserId(int $userId);
    
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id);
}