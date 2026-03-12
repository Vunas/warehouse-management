<?php

namespace App\Services;

use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleService
{
    protected $roleRepo;

    public function __construct(RoleRepositoryInterface $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    public function getAllRoles()
    {
        return $this->roleRepo->all(['*'], ['permissions']);
    }

    public function createRole(array $data)
    {
        return $this->roleRepo->create($data);
    }

    public function updateRole($id, array $data)
    {
        return $this->roleRepo->update($id, $data);
    }

    public function deleteRole($id)
    {
        return $this->roleRepo->delete($id);
    }
}