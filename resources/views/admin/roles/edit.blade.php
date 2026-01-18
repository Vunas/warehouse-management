@extends('layouts.admin')

@section('title', 'Chỉnh sửa Vai trò')
@section('header', 'Cập nhật Vai trò: ' . $role->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Role Name -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Tên Vai trò <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $role->name) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" {{ in_array($role->name, ['Admin', 'Manager', 'Staff']) ? 'readonly' : '' }}>
            @if(in_array($role->name, ['Admin', 'Manager', 'Staff']))
                <p class="text-xs text-orange-500 mt-1"><i class="fa-solid fa-triangle-exclamation"></i> Tên vai trò hệ thống không thể thay đổi.</p>
            @endif
            @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Permissions Matrix -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-700">Điều chỉnh quyền hạn</h3>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($permissions as $groupName => $perms)
                <div class="border rounded-lg p-4 bg-gray-50/50">
                    <h4 class="font-bold text-blue-600 uppercase text-xs mb-3 border-b pb-2 flex items-center">
                        <i class="fa-solid fa-layer-group mr-2"></i> Module: {{ ucfirst($groupName) }}
                    </h4>
                    <div class="space-y-2">
                        @foreach($perms as $perm)
                        <label class="flex items-start cursor-pointer hover:bg-white p-1 rounded transition">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="permission_ids[]" value="{{ $perm->id }}" 
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-gray-700">{{ $perm->code }}</span>
                                <p class="text-xs text-gray-500">{{ $perm->description }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('roles.index') }}" class="px-4 py-2 text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Hủy bỏ</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-sm">Cập nhật</button>
        </div>
    </form>
</div>
@endsection