@extends('layouts.admin')

@section('title', 'Thêm Nhân viên')
@section('header', 'Tạo mới Nhân viên')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        
        <!-- User Info -->
        <h3 class="text-sm font-bold text-gray-700 uppercase mb-4 pb-2 border-b">Thông tin tài khoản</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                @error('full_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập <span class="text-red-500">*</span></label>
                <input type="text" name="username" value="{{ old('username') }}" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                @error('username') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <!-- MẬT KHẨU & XÁC NHẬN -->
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Employee Info -->
        <h3 class="text-sm font-bold text-gray-700 uppercase mb-4 pb-2 border-b">Thông tin công việc</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ <span class="text-red-500">*</span></label>
                <input type="text" name="position" value="{{ old('position') }}" class="w-full border rounded px-3 py-2 text-sm" placeholder="VD: Thủ kho">
                @error('position') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày tuyển dụng <span class="text-red-500">*</span></label>
                <input type="date" name="hired_at" value="{{ old('hired_at') }}" class="w-full border rounded px-3 py-2 text-sm">
                @error('hired_at') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kho làm việc chính</label>
            <select name="warehouse_id" class="w-full border rounded px-3 py-2 text-sm bg-white">
                <option value="">-- Chọn Kho (Nếu có) --</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }} ({{ $wh->type->type_code }})</option>
                @endforeach
            </select>
            @error('warehouse_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò hệ thống <span class="text-red-500">*</span></label>
            <div class="flex flex-wrap gap-4 mt-2 bg-gray-50 p-3 rounded border border-gray-200">
                @foreach($roles as $role)
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" 
                        class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                        {{ in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                </label>
                @endforeach
            </div>
            @error('role_ids') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('employees.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 rounded">Hủy bỏ</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded text-sm font-medium hover:bg-blue-700">Lưu nhân viên</button>
        </div>
    </form>
</div>
@endsection