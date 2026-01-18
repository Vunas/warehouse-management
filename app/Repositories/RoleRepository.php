<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->withCount('employees')->get();
    }

    public function findById($id)
    {
        return $this->model->with('permissions')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $role = $this->findById($id);
        $role->update($data);
        return $role;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
    
    // Hàm đặc thù: Đồng bộ quyền cho vai trò
    public function syncPermissions($roleId, array $permissionIds)
    {
        $role = $this->findById($roleId);
        return $role->permissions()->sync($permissionIds);
    }
}