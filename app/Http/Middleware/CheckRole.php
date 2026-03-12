<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Cách dùng ở Route: Route::get('/admin/dashboard')->middleware('role:admin|manager');
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
        }

        $allowedRoles = explode('|', $roles);
        $hasRole = false;

        foreach ($allowedRoles as $role) {
            if (auth()->user()->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return response()->json(['success' => false, 'message' => 'Khu vực cấm truy cập!'], 403);
        }

        return $next($request);
    }
}