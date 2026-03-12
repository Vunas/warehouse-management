<?php

namespace App\Services;

use App\Repositories\Interfaces\PermissionRepositoryInterface;

class PermissionService
{
    protected $permissionRepo;

    public function __construct(PermissionRepositoryInterface $permissionRepo)
    {
        $this->permissionRepo = $permissionRepo;
    }

    public function getAllPermissions()
    {
        return $this->permissionRepo->all();
    }

    public function createPermission(array $data)
    {
        return $this->permissionRepo->create($data);
    }

    public function updatePermission($id, array $data)
    {
        return $this->permissionRepo->update($id, $data);
    }

    public function deletePermission($id)
    {
        return $this->permissionRepo->delete($id);
    }
}