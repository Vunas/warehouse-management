<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class RoleService
{
    protected $roleRepo;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    public function getAllRoles()
    {
        return $this->roleRepo->getAll();
    }

    public function createRole(array $data)
    {
        DB::beginTransaction();
        try {
            $role = $this->roleRepo->create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            if (!empty($data['permission_ids'])) {
                $this->roleRepo->syncPermissions($role->id, $data['permission_ids']);
            }

            DB::commit();
            return $role;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateRole($id, array $data)
    {
        DB::beginTransaction();
        try {
            $role = $this->roleRepo->update($id, ['name' => $data['name']]);

            if (isset($data['permission_ids'])) {
                $this->roleRepo->syncPermissions($id, $data['permission_ids']);
            }

            DB::commit();
            return $role;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteRole($id)
    {
        // Logic kiểm tra: Không được xóa Role hệ thống (Admin)
        $role = $this->roleRepo->findById($id);
        if (in_array($role->name, ['Admin', 'Manager', 'Staff'])) {
            throw new Exception("Không thể xóa vai trò mặc định của hệ thống.");
        }
        
        return $this->roleRepo->delete($id);
    }
}