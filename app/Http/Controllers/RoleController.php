<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use App\Http\Requests\Role\RoleRequest;
use Illuminate\Http\Request;
use Exception;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
        // Tự động map với RolePolicy
        $this->authorizeResource(Role::class, 'role');
    }

    public function index(Request $request)
    {
        $roles = $this->roleService->getPaginatedRoles(
            $request->get('per_page', 15),
            $request->get('search', '')
        );

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->roleService->getAllPermissions();
        return view('admin.roles.form', compact('permissions'));
    }

    public function store(RoleRequest $request)
    {
        try {
            $this->roleService->createRole($request->validated());
            return redirect()->route('roles.index')->with('success', 'Tạo vai trò thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        $permissions = $this->roleService->getAllPermissions();
        // Lấy danh sách tên các quyền mà role này đang có để check checked
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.form', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        try {
            $this->roleService->updateRole($role, $request->validated());
            return redirect()->route('roles.index')->with('success', 'Cập nhật vai trò thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        try {
            $this->roleService->deleteRole($role);
            return redirect()->route('roles.index')->with('success', 'Xóa vai trò thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}