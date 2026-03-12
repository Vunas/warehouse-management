<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permissionCode)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
        }

        // Bỏ qua check quyền nếu là Super Admin (Giả sử role_name là 'super_admin')
        if (auth()->user()->hasRole('super_admin')) {
            return $next($request);
        }

        // Kiểm tra quyền
        if (!auth()->user()->hasPermission($permissionCode)) {
            return response()->json([
                'success' => false, 
                'message' => 'Bạn không có quyền thực hiện hành động này! (Cần quyền: ' . $permissionCode . ')'
            ], 403);
        }

        return $next($request);
    }
}