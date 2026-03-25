<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class UserService
{
    /**
     * Lấy danh sách user có phân trang và lọc
     */
    public function getPaginatedUsers($perPage = 15, $filters = [], $sort = 'id', $dir = 'desc')
    {
        $query = User::query()->with('roles'); // Eager load roles để tránh N+1 query

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('username', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', $filters['status']);
        }

        return $query->orderBy($sort, $dir)->paginate($perPage)->withQueryString();
    }

    /**
     * Lấy tất cả vai trò để hiển thị ở Form
     */
    public function getAllRoles()
    {
        return Role::all();
    }

    /**
     * Tạo người dùng mới
     */
    public function createUser(array $data)
    {
        DB::beginTransaction();
        try {
            $data['password'] = Hash::make($data['password']);
            
            $user = User::create($data);
            
            // Gán vai trò cho user
            if (isset($data['role_name'])) {
                $user->assignRole($data['role_name']);
            }

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật người dùng
     */
    public function updateUser(User $user, array $data)
    {
        DB::beginTransaction();
        try {
            // Xử lý mật khẩu
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']); // Xóa key password nếu trống để không update
            }

            $user->update($data);

            // Cập nhật lại vai trò
            if (isset($data['role_name'])) {
                $user->syncRoles([$data['role_name']]);
            }

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Xóa mềm người dùng
     */
    public function softDeleteUser(User $user)
    {
        // Có thể thêm logic: không cho xóa Admin cao nhất ở đây
        return $user->delete();
    }
}
