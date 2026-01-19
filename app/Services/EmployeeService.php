<?php

namespace App\Services;

use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EmployeeService
{
    protected $employeeRepo;
    protected $userRepo;

    public function __construct(
        EmployeeRepositoryInterface $employeeRepo,
        UserRepositoryInterface $userRepo
    ) {
        $this->employeeRepo = $employeeRepo;
        $this->userRepo = $userRepo;
    }

    public function getEmployeesPaginated()
    {
        return $this->employeeRepo->paginate();
    }

    public function getEmployeeById($id)
    {
        return $this->employeeRepo->findById($id);
    }

    public function getEmployeeSelection()
    {
        return $this->employeeRepo->getSelectable();
    }

    public function createEmployee(array $data)
    {
        DB::beginTransaction();
        try {
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'full_name' => $data['full_name'],
                'is_active' => true,
            ];
            $user = $this->userRepo->create($userData);

            $employeeData = [
                'user_id' => $user->id,
                'employee_code' => $data['employee_code'] ?? 'EMP' . time(),
                'position' => $data['position'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'hired_at' => $data['hired_at'],
                'role_ids' => $data['role_ids'] ?? []
            ];

            $employee = $this->employeeRepo->create($employeeData);

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

            $this->userRepo->update($employee->user_id, [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            $updateData = [
                'position' => $data['position'],
                'warehouse_id' => $data['warehouse_id'],
                'hired_at' => $data['hired_at'],
            ];

            if (isset($data['role_ids'])) {
                $updateData['role_ids'] = $data['role_ids'];
            }

            $updatedEmployee = $this->employeeRepo->update($id, $updateData);

            DB::commit();
            return $updatedEmployee;
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
