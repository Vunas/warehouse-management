<?php

namespace App\Services;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class RoleService
{
    protected $roleRepo;

    public function __construct(RoleRepositoryInterface $roleRepo)
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
            // Chuẩn bị dữ liệu, map permission_ids sang permissions cho Repo xử lý
            $repoData = [
                'name' => $data['name'],
                'guard_name' => 'web',
                'permissions' => $data['permission_ids'] ?? []
            ];

            $role = $this->roleRepo->create($repoData);

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
            $repoData = ['name' => $data['name']];
            
            if (isset($data['permission_ids'])) {
                $repoData['permissions'] = $data['permission_ids'];
            }

            $role = $this->roleRepo->update($id, $repoData);

            DB::commit();
            return $role;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteRole($id)
    {
        $role = $this->roleRepo->findById($id);
        if (in_array($role->name, ['Admin', 'Manager', 'Staff'])) {
            throw new Exception("Không thể xóa vai trò mặc định của hệ thống.");
        }
        
        return $this->roleRepo->delete($id);
    }
}