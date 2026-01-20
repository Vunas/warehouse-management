<?php

namespace App\Repositories\Interfaces;

interface EmployeeRepositoryInterface
{

    public function paginate($perPage = 10);
    public function findById($id);
    public function findByUserId($userId);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getSelectable();
    public function getByWarehouse($warehouseId);
}
