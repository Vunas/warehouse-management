@extends('layouts.admin')

@php
    $isEdit = isset($user);
    $action = $isEdit ? route('users.update', $user->id) : route('users.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Cập nhật Người dùng' : 'Thêm Người dùng mới';
@endphp

@section('content')
    <!-- Gọi component crud.form -->
    <x-crud.form 
        :title="$title" 
        :action="$action" 
        :method="$method" 
        cancelRoute="{{ route('users.index') }}"
    >
        <!-- Gọi component input, tự động handle label, old() value và báo lỗi -->
        <x-ui.input 
            name="username" 
            label="Tên đăng nhập" 
            :value="$user->username ?? ''" 
            required="true" 
        />

        <x-ui.input 
            name="full_name" 
            label="Họ và Tên" 
            :value="$user->full_name ?? ''" 
            required="true" 
        />

        <x-ui.input 
            name="email" 
            type="email" 
            label="Email liên hệ" 
            :value="$user->email ?? ''" 
            required="true" 
        />

        <x-ui.input 
            name="password" 
            type="password" 
            label="{{ $isEdit ? 'Mật khẩu mới (Để trống nếu không đổi)' : 'Mật khẩu' }}" 
            required="{{ !$isEdit }}" 
        />

        <!-- Trạng thái -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="is_active" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="1" {{ old('is_active', $user->is_active ?? 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ old('is_active', $user->is_active ?? 1) == 0 ? 'selected' : '' }}>Khóa</option>
            </select>
        </div>
    </x-crud.form>
@endsection