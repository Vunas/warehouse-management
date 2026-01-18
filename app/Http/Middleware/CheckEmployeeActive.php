<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEmployeeActive
{
    public function handle(Request $request, Closure $next)
    {
        //nếu chưa đăng nhập thì cút
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        //nếu không phải emplotee thì cút
        if (!$user->employee) {
            return redirect()->route('customer.dashboard');
        }

        if ($user && !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['account' => 'Tài khoản của bạn đã bị vô hiệu hóa.']);
        }

        return $next($request);
    }
}