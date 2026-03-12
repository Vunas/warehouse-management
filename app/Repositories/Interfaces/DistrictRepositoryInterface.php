<?php

namespace App\Repositories\Interfaces;

interface DistrictRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByCityId(int $cityId);
    
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id);
}