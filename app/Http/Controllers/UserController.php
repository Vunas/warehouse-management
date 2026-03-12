<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Exception;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Hiển thị danh sách người dùng
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $users = $this->userService->getPaginatedUsers($perPage);
        
        // Trả về view: resources/views/admin/users/index.blade.php
        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị form tạo mới người dùng
     */
    public function create()
    {
        // SỬA Ở ĐÂY: Trỏ đúng vào thư mục admin.users
        return view('admin.users.form');
    }

    /**
     * Xử lý lưu người dùng mới (Nhận data từ form create)
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $this->userService->createUser($request->validated());
            
            return redirect()->route('users.index')
                             ->with('success', 'Tạo người dùng thành công!');
        } catch (Exception $e) {
            return back()->withInput()
                         ->with('error', 'Lỗi khi tạo người dùng: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa người dùng
     */
    public function edit($id)
    {
        try {
            $user = $this->userService->getUserById($id);
            // SỬA Ở ĐÂY: Dùng chung file form.blade.php với create, chỉ khác là có truyền thêm biến $user
            return view('admin.users.form', compact('user'));
        } catch (Exception $e) {
            return redirect()->route('users.index')
                             ->with('error', 'Không tìm thấy người dùng để chỉnh sửa.');
        }
    }

    /**
     * Xử lý cập nhật người dùng (Nhận data từ form edit)
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $this->userService->updateUser($id, $request->validated());
            
            return redirect()->route('users.index')
                             ->with('success', 'Cập nhật người dùng thành công!');
        } catch (Exception $e) {
            return back()->withInput()
                         ->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý xóa người dùng
     */
    public function destroy($id)
    {
        try {
            $this->userService->softDeleteUser($id);
            
            return redirect()->route('users.index')
                             ->with('success', 'Đã chuyển người dùng vào thùng rác!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
}