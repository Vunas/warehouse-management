<?php

namespace App\Repositories\Interfaces;

interface CityRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id);
}