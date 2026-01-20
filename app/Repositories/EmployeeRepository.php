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

    public function paginate($perPage = 10)
    {
        return $this->model->with(['user', 'warehouse', 'roles'])->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return $this->model->with(['user', 'roles', 'warehouse'])->findOrFail($id);
    }
    public function findByUserId($userId)
    {
        return $this->model->with(['user', 'roles', 'warehouse'])->where('user_id', $userId)->first();
    }

    public function create($data)
    {
        $employee = $this->model->create($data);
        
        // Nếu có role_ids thì sync luôn
        if (isset($data['role_ids'])) {
            $employee->roles()->sync($data['role_ids']);
        }

        return $employee;
    }

    public function update($id, $data)
    {
        $employee = $this->findById($id);
        $employee->update($data);

        if (isset($data['role_ids'])) {
            $employee->roles()->sync($data['role_ids']);
        }

        return $employee;
    }

    public function delete($id)
    {
        $employee = $this->findById($id);
        
        // Logic xóa user đi kèm nếu cần, hoặc chỉ soft delete employee
        if ($employee->user) {
            $employee->user()->delete(); 
        }

        return $employee->delete();
    }

    public function getSelectable()
    {
        // Trả về danh sách rút gọn cho dropdown select box
        return $this->model->select('id', 'employee_code', 'user_id')
                    ->with('user:id,full_name') // Giả sử User có full_name
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->employee_code . ' - ' . ($item->user->full_name ?? 'N/A')
                        ];
                    });
    }

    public function getByWarehouse($warehouseId)
    {
        return $this->model->where('warehouse_id', $warehouseId)
                           ->with('user')
                           ->get();
    }
}