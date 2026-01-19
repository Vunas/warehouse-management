<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCustomerActive
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Chưa đăng nhập
        if (!Auth::check()) {
            return redirect('/customer/login');
        }

        $user = Auth::user();

        // 2. Nếu là nhân viên → chặn
        if ($user->employee) {
            abort(403, 'Trang này chỉ dành cho khách hàng');
        }

        // 3. Nếu bị khóa
        if (!$user->is_active) {
            Auth::logout();

            return redirect('/customer/login')
                ->withErrors([
                    'account' => 'Tài khoản của bạn đã bị vô hiệu hóa.'
                ]);
        }

        // 4. OK
        return $next($request);
    }
}
