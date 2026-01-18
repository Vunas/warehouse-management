<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
        // Chỉ Admin mới được can thiệp vào Role
        // $this->authorizeResource(Role::class); 
    }

    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($item) {
            return explode('.', $item->code)[0]; // Group by module (employee, warehouse...)
        });
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission_ids' => 'array'
        ]);

        $this->roleService->createRole($request->all());
        return redirect()->route('roles.index')->with('success', 'Tạo vai trò thành công');
    }

    public function edit($id)
    {
        // Repository getById thường trả về Model, nên ta truy cập relation được
        $role = $this->roleService->getAllRoles()->find($id); 
        $permissions = Permission::all()->groupBy(function($item) {
            return explode('.', $item->code)[0];
        });
        
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
            'permission_ids' => 'array'
        ]);

        $this->roleService->updateRole($id, $request->all());
        return redirect()->route('roles.index')->with('success', 'Cập nhật vai trò thành công');
    }
}