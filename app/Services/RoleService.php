<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Exception;

class RoleService
{
    public function getPaginatedRoles($perPage = 15, $search = '')
    {
        $query = Role::with('permissions'); // Kèm theo quyền để hiển thị

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getAllPermissions()
    {
        return Permission::all();
    }

    public function createRole(array $data)
    {
        DB::beginTransaction();
        try {
            // Spatie tự động tạo role
            $role = Role::create(['name' => $data['name']]);
            
            // Gán quyền cho role (nếu có chọn)
            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return $role;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateRole(Role $role, array $data)
    {
        DB::beginTransaction();
        try {
            $role->update(['name' => $data['name']]);

            // Cập nhật lại danh sách quyền (Spatie sẽ tự xóa quyền cũ, map quyền mới)
            $permissions = $data['permissions'] ?? [];
            $role->syncPermissions($permissions);

            DB::commit();
            return $role;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteRole(Role $role)
    {
        // Spatie sẽ tự động gỡ các khóa ngoại liên quan trước khi xóa
        return $role->delete();
    }
}