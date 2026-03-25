<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        // Nếu đã đăng nhập thì chuyển thẳng vào trang admin
        if (Auth::check()) {
            return redirect()->route('users.index');
        }
        
        return view('auth.login');
    }

    /**
     * Xử lý logic đăng nhập
     */
    public function login(Request $request)
    {
        // 1. Validate dữ liệu nhập vào
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        // 2. Thử đăng nhập với thông tin được cung cấp
        $remember = $request->boolean('remember'); // Tính năng ghi nhớ đăng nhập
        
        if (Auth::attempt($credentials, $remember)) {
            
            // 3. Kiểm tra xem tài khoản có bị khóa không
            if (!Auth::user()->is_active) {
                Auth::logout(); // Đăng xuất ngay lập tức
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.',
                ])->onlyInput('email');
            }

            // 4. Đăng nhập thành công -> Tạo lại session để bảo mật (tránh Session Fixation)
            $request->session()->regenerate();

            // Chuyển hướng về trang họ muốn vào trước đó, hoặc mặc định về /admin/users
            return redirect()->intended('/admin/users')->with('success', 'Đăng nhập thành công!');
        }

        // 5. Đăng nhập thất bại -> Trả về lỗi
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Xóa sạch session hiện tại
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Bạn đã đăng xuất an toàn.');
    }
}