<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Models\Role;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Log;

use function Illuminate\Log\log;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
        $this->authorizeResource(Employee::class, 'employee');
    }

    public function index()
    {
        $employees = $this->employeeService->getAllEmployees();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {

        $roles = Role::all();
        $warehouses = Warehouse::all();
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
        $employee->load(['user', 'roles', 'warehouse']);
        $roles = Role::all();
        $warehouses = Warehouse::all();

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
