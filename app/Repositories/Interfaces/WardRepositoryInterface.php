<?php

namespace App\Repositories\Interfaces;

interface WardRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);
    public function findById($id, array $columns = ['*'], array $relations = []);
    public function getByDistrictId(int $districtId);
    
    public function create(array $payload);
    public function update($id, array $payload);
    public function delete($id);
}