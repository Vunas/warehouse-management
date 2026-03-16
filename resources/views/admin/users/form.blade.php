@extends('layouts.admin')

@php
    $isEdit = isset($user);
    $action = $isEdit ? route('users.update', $user->id) : route('users.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Cập nhật Người dùng' : 'Thêm Người dùng mới';
@endphp

@section('content')
    <x-crud.form 
        :title="$title" 
        :action="$action" 
        :method="$method" 
        cancelRoute="{{ route('users.index') }}"
    >
        <!-- Cột 1 -->
        <div class="space-y-6">
            <x-ui.input 
                name="username" 
                label="Tên đăng nhập" 
                :value="old('username', $user->username ?? '')" 
                required="true" 
                placeholder="Ví dụ: admin_123"
            />

            <x-ui.input 
                name="email" 
                type="email" 
                label="Email liên hệ" 
                :value="old('email', $user->email ?? '')" 
                required="true" 
                placeholder="admin@example.com"
            />

            <x-ui.input 
                name="password" 
                type="password" 
                label="{{ $isEdit ? 'Mật khẩu mới (Để trống nếu không đổi)' : 'Mật khẩu' }}" 
                required="{{ !$isEdit }}" 
                placeholder="••••••••"
            />
        </div>

        <!-- Cột 2 -->
        <div class="space-y-6">
            <x-ui.input 
                name="full_name" 
                label="Họ và Tên" 
                :value="old('full_name', $user->full_name ?? '')" 
                required="true" 
                placeholder="Nhập họ và tên đầy đủ"
            />

            <!-- Component Chọn Vai Trò (Role) -->
            <div>
                <label for="role_id" class="block text-sm font-semibold text-gray-700 mb-2">Vai trò (Role)</label>
                <select name="role_id" id="role_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out bg-white">
                    <option value="">-- Chọn một vai trò --</option>
                    @if(isset($roles))
                        @foreach($roles as $role)
                            @php
                                // Logic kiểm tra role hiện tại (Sửa thành logic của bạn nếu xài Spatie)
                                $selectedRole = isset($user) && $user->roles->count() ? $user->roles->first()->id : null;
                            @endphp
                            <option value="{{ $role->id }}" {{ old('role_id', $selectedRole) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <!-- Trạng thái (Chiếm toàn bộ độ rộng ở dưới cùng) -->
        <div class="md:col-span-2 mt-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái tài khoản</label>
            <div class="flex items-center space-x-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="is_active" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('is_active', $user->is_active ?? 1) == 1 ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700 font-medium">Hoạt động bình thường</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="is_active" value="0" class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500" {{ old('is_active', $user->is_active ?? 1) == 0 ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700 font-medium">Khóa tài khoản</span>
                </label>
            </div>
        </div>

    </x-crud.form>
@endsection