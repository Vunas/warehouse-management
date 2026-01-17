<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    protected $model;

    public function __construct(Employee $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated($perPage = 10)
    {
        return $this->model->with(['user', 'warehouse', 'roles'])->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with(['user', 'roles'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->findById($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->findById($id);
        // Soft delete user liên quan nếu cần
        $record->user()->delete(); 
        return $record->delete();
    }

    public function getByWarehouse($warehouseId)
    {
        return $this->model->where('warehouse_id', $warehouseId)->get();
    }
}