<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register'); // form đăng ký khách
    }

    public function showCustomerLogin()
    {
        return view('auth.customer_login');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            // Nếu KHÔNG phải nhân viên → chặn
            if (!Auth::user()->employee) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Trang này chỉ dành cho nhân viên'
                ]);
            }

            //Check khóa account
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors(['username' => 'Tài khoản đã bị khóa.']);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors([
                'username' => 'Thông tin đăng nhập không chính xác.',
            ])
            ->withInput($request->only('username'));
    }

    //------Login cho khách
    public function customerLogin(Request $request)
    {
        // Validate form
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Thử đăng nhập
        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            // Nếu là nhân viên → chặn
            if (Auth::user()->employee) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Trang này chỉ dành cho khách hàng'
                ]);
            }

            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Tài khoản đã bị khóa'
                ]);
            }

            // KHÁCH ĐÚNG
            return redirect()->route('customer.dashboard');
            ;
        }

        return back()->withErrors([
            'username' => 'Sai tài khoản hoặc mật khẩu'
        ]);
    }

    //------Register
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'full_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        // 1. Tạo user
        $user = User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => 1
        ]);

        // 2. Tạo customer gắn với user
        \App\Models\Customer::create([
            'user_id' => $user->id,
            'company_name' => 'Chưa cập nhật', // tạm
            'billing_phone' => null,
            'address' => null
        ]);

        return redirect('/customer/login')
            ->with('success', 'Đăng ký thành công! Hãy đăng nhập lại');
    }



    //------LogOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
