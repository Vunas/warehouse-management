<?php

namespace App\Repositories\Interfaces;

interface RoleRepositoryInterface
{
    public function getAll();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function syncPermissions($roleId,array $permissionIds);
}