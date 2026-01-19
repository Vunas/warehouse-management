<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\RoleService;
use App\Services\WarehouseService;

class EmployeeController extends Controller
{
    protected $employeeService;
    protected $roleService;
    protected $warehouseService;

    public function __construct(
        EmployeeService $employeeService,
        RoleService $roleService,
        WarehouseService $warehouseService
    ) {
        $this->employeeService = $employeeService;
        $this->roleService = $roleService;
        $this->warehouseService = $warehouseService;
        $this->authorizeResource(Employee::class, 'employee');
    }

    public function index()
    {
        $employees = $this->employeeService->getEmployeesPaginated();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $roles = $this->roleService->getAllRoles(); 
        $warehouses = $this->warehouseService->getWarehouseSelection(); 

        return view('admin.employees.create', compact('roles', 'warehouses'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $this->employeeService->createEmployee($request->validated());

        return redirect()->route('employees.index')
            ->with('success', 'Tạo nhân viên thành công!');
    }

    public function edit(Employee $employee)
    {
        $employee = $this->employeeService->getEmployeeById($employee->id);
        
        $roles = $this->roleService->getAllRoles();
        $warehouses = $this->warehouseService->getWarehouseSelection();

        return view('admin.employees.edit', compact('employee', 'roles', 'warehouses'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->employeeService->updateEmployee($employee->id, $request->validated());

        return redirect()->route('employees.index')
            ->with('success', 'Cập nhật nhân viên thành công!');
    }

    public function destroy(Employee $employee)
    {
        $this->employeeService->deleteEmployee($employee->id);

        return redirect()->route('employees.index')
            ->with('success', 'Đã xóa nhân viên.');
    }
}