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
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard'); // admin
        }
        return view('auth.login');
    }

    public function showCustomerLoginForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard'); // customer
        }
        return view('auth.customer_login');
    }

    /**
     * Xử lý logic đăng nhập
     */
    public function login(Request $request, $type = null)
    {
        $guard = $type ?? $request->input('type', 'web');


        // 1. Validate
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->boolean('remember');

        // 2. Attempt login
        if (Auth::guard($guard)->attempt($credentials, $remember)) {
            $user = Auth::guard($guard)->user();

            if (!$user->is_active) {
                Auth::guard($guard)->logout();
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            if ($guard === 'web') {
                return redirect()->route('dashboard')
                    ->with('success', 'Đăng nhập thành công!');
            } else {
                return redirect()->route('customer.dashboard')
                    ->with('success', 'Đăng nhập thành công!');
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }
    /**
     * Xử lý đăng xuất
     */
    public function logout(Request $request, $guard = 'web')
    {
        Auth::guard('web')->logout();
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($guard === 'customer') {
            return redirect()->route('customer_login')
                ->with('success', 'Bạn đã đăng xuất thành công!');
        } else {
            return redirect()->route('login')
                ->with('success', 'Bạn đã đăng xuất thành công!');
        }
    }
}