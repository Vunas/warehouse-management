@extends('layouts.admin')

@section('title', 'Quản lý Vai trò')
@section('header', 'Danh sách Vai trò & Phân quyền')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <p class="text-sm text-gray-500">Quản lý các nhóm quyền hạn trong hệ thống.</p>
        <a href="{{ route('roles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 shadow-sm">
            <i class="fa-solid fa-plus mr-1"></i> Thêm Vai trò
        </a>
    </div>

    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3">Tên Vai trò</th>
                <th class="px-6 py-3">Số lượng nhân viên</th>
                <th class="px-6 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($roles as $role)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <span class="font-bold text-gray-800">{{ $role->name }}</span>
                    @if(in_array($role->name, ['Admin', 'Manager', 'Staff']))
                        <span class="ml-2 bg-blue-100 text-blue-700 text-[10px] px-2 py-0.5 rounded-full uppercase font-bold">System</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                        <i class="fa-solid fa-users mr-1"></i> {{ $role->employees_count ?? 0 }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('roles.edit', $role->id) }}" class="text-blue-600 hover:text-blue-800" title="Chỉnh sửa quyền">
                        <i class="fa-solid fa-shield-halved"></i> Phân quyền
                    </a>
                    
                    @if(!in_array($role->name, ['Admin', 'Manager', 'Staff']))
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa vai trò này sẽ ảnh hưởng đến các nhân viên đang nắm giữ. Tiếp tục?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection