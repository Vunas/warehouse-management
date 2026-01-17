<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class EmployeeService
{
    protected $employeeRepo;

    public function __construct(EmployeeRepositoryInterface $employeeRepo)
    {
        $this->employeeRepo = $employeeRepo;
    }

    public function getAllEmployees()
    {
        return $this->employeeRepo->getAllPaginated();
    }

    // --- FIX: Thêm method này để Controller gọi ---
    public function getEmployeeById($id)
    {
        return $this->employeeRepo->findById($id);
    }
    // ---------------------------------------------

    public function createEmployee(array $data)
    {
        DB::beginTransaction();
        try {
            // 1. Tạo User trước
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'full_name' => $data['full_name'],
                'is_active' => true,
            ]);

            // 2. Tạo Employee liên kết với User
            $employeeData = [
                'user_id' => $user->id,
                'position' => $data['position'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'hired_at' => $data['hired_at'],
            ];
            $employee = $this->employeeRepo->create($employeeData);

            // 3. Gán Roles (Relationship Many-to-Many)
            if (!empty($data['role_ids'])) {
                $employee->roles()->sync($data['role_ids']);
            }

            DB::commit();
            return $employee;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Lỗi tạo nhân viên: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateEmployee($id, array $data)
    {
        DB::beginTransaction();
        try {
            $employee = $this->employeeRepo->findById($id);

            // 1. Update thông tin User
            $employee->user->update([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? $employee->user->is_active,
            ]);

            // 2. Update thông tin Employee
            $this->employeeRepo->update($id, [
                'position' => $data['position'],
                'warehouse_id' => $data['warehouse_id'],
                'hired_at' => $data['hired_at'],
            ]);

            // 3. Sync Roles nếu có thay đổi
            if (isset($data['role_ids'])) {
                $employee->roles()->sync($data['role_ids']);
            }

            DB::commit();
            return $employee;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteEmployee($id)
    {
        return $this->employeeRepo->delete($id);
    }
}