<?php

namespace App\Repositories\Interfaces;

interface LocationRepositoryInterface
{
    public function all(array $columns = ['*'], array $with = []);
    public function findById($id, array $columns = ['*'], array $with = []);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    public function getByWarehouse(int $warehouseId);
    public function getChildren(int $parentId);
    public function getRootLocations(int $warehouseId);
}
