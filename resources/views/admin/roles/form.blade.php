@extends('layouts.admin')

@php
    $isEdit = isset($role);
    $action = $isEdit ? route('roles.update', $role->id) : route('roles.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Cập nhật Vai trò' : 'Thêm Vai trò mới';
    
    // Gán biến array trống nếu tạo mới
    $rolePermissions = $rolePermissions ?? [];
@endphp

@section('content')
    <x-crud.form 
        :title="$title" 
        :action="$action" 
        :method="$method" 
        cancelRoute="{{ route('roles.index') }}"
    >
        <!-- Khối nhập tên Role -->
        <div class="md:col-span-2">
            <x-ui.input 
                name="name" 
                label="Tên Vai trò (Role Name)" 
                :value="old('name', $role->name ?? '')" 
                required="true" 
                placeholder="VD: manager, warehouse_staff..." 
                :disabled="$isEdit && $role->name === 'admin'"
            />
            @if($isEdit && $role->name === 'admin')
                <p class="mt-1 text-sm text-red-500">Vai trò mặc định "admin" không thể đổi tên.</p>
                <input type="hidden" name="name" value="admin">
            @endif
        </div>

        <!-- Khối chọn Quyền (Permissions) -->
        <div class="md:col-span-2 mt-4">
            <label class="block text-sm font-bold text-gray-700 mb-3 border-b pb-2">Cấp quyền cho vai trò này</label>
            
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                @foreach($permissions as $permission)
                    <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-white rounded transition-colors">
                        <input type="checkbox" 
                               name="permissions[]" 
                               value="{{ $permission->name }}" 
                               class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                               {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                               {{ $isEdit && $role->name === 'admin' ? 'disabled checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
            
            @if($isEdit && $role->name === 'admin')
                <p class="mt-2 text-sm text-red-500 italic">* Vai trò Admin luôn tự động có tất cả các quyền.</p>
            @endif
            
            @error('permissions')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </x-crud.form>
@endsection