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

    public function create($data)
    {
        $role = $this->model->create($data);
        
        if (isset($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return $role;
    }

    public function update($id, $data)
    {
        $role = $this->findById($id);
        $role->update($data);

        if (isset($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return $role;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getSelectable()
    {
        return $this->model->select('id', 'role_name as name')->get();
    }

    public function syncPermissions($roleId, $permissionIds)
    {
        $role = $this->findById($roleId);
        return $role->permissions()->sync($permissionIds);
    }
}