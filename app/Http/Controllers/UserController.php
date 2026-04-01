<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Exception;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'role', 'include_inactive']);
        $roles = $this->userService->getAllRoles();
        $users = $this->userService->getPaginatedUsers(
            $request->get('per_page', 15),
            $filters,
            $request->get('sort', 'id'),
            $request->get('dir', 'asc')
        );

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = $this->userService->getAllRoles();
        return view('admin.users.form', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $this->userService->createUser($request->validated());
            return redirect()->route('users.index')->with('success', 'Tạo người dùng thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    public function edit(User $user)
    {
        $roles = $this->userService->getAllRoles();
        return view('admin.users.form', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $this->userService->updateUser($user, $request->validated());
            return redirect()->route('users.index')->with('success', 'Cập nhật thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->softDeleteUser($user);
            return redirect()->route('users.index')->with('success', 'Đã chuyển vào thùng rác!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function restore(User $user)
    {
        try {
            $this->userService->restoreUser($user);
            return redirect()->route('users.index')->with('success', 'Đã khôi phục người dùng!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }   
    
    public function forceDelete(User $user)
    {
        try {
            $this->userService->forceDeleteUser($user);
            return redirect()->route('users.index')->with('success', 'Đã xóa vĩnh viễn người dùng!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
