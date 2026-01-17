@extends('layouts.admin')

@section('title', 'Quản lý Nhân viên')
@section('header', 'Danh sách Nhân viên')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <form action="" method="GET" class="flex gap-2">
            <input type="text" name="search" placeholder="Tìm tên, email..." class="border rounded px-3 py-1.5 text-sm w-64">
            <button type="submit" class="bg-gray-100 px-3 py-1.5 rounded text-sm hover:bg-gray-200">Tìm</button>
        </form>
        @can('create', App\Models\Employee::class)
            <a href="{{ route('employees.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                <i class="fa-solid fa-plus mr-1"></i> Thêm nhân viên
            </a>
        @endcan
    </div>

    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3">Nhân viên</th>
                <th class="px-6 py-3">Chức vụ</th>
                <th class="px-6 py-3">Kho làm việc</th>
                <th class="px-6 py-3">Vai trò</th>
                <th class="px-6 py-3">Trạng thái</th>
                <th class="px-6 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($employees as $emp)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">{{ $emp->user->full_name }}</div>
                    <div class="text-xs text-gray-500">{{ $emp->user->email }}</div>
                </td>
                <td class="px-6 py-4">{{ $emp->position }}</td>
                <td class="px-6 py-4">
                    @if($emp->warehouse)
                        <span class="text-blue-600">{{ $emp->warehouse->name }}</span>
                    @else
                        <span class="text-gray-400">---</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @foreach($emp->roles as $role)
                        <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td class="px-6 py-4">
                    @if($emp->user->is_active)
                        <span class="text-green-600 bg-green-50 px-2 py-1 rounded-full text-xs font-bold">Active</span>
                    @else
                        <span class="text-red-600 bg-red-50 px-2 py-1 rounded-full text-xs font-bold">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    @can('update', $emp)
                        <a href="{{ route('employees.edit', $emp->id) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen"></i></a>
                    @endcan
                    @can('delete', $emp)
                        <form action="{{ route('employees.destroy', $emp) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="p-4 border-t border-gray-100">
        {{ $employees->links() }}
    </div>
</div>
@endsection