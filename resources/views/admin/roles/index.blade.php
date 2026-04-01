@extends('layouts.admin')

@section('content')
    @php
        $createRoute = auth()->user()->can('create', Spatie\Permission\Models\Role::class) ? route('roles.create') : '';
    @endphp

    <x-crud.index 
        title="Quản lý Vai trò (Roles)" 
        :createRoute="$createRoute" 
        :data="$roles"
    >
        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('roles.index') }}">
                <div class="relative w-full md:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên vai trò..." class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">ID</x-ui.table.column>
                <x-ui.table.column name="name">Tên Vai trò</x-ui.table.column>
                <x-ui.table.column name="permissions">Các quyền sở hữu</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($roles as $role)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ $role->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                        {{ strtoupper($role->name) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div class="flex flex-wrap gap-1">
                            @if($role->name === 'admin')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Tất cả quyền (Super Admin)</span>
                            @elseif($role->name === 'customer')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Tài khoản chỉ sử dụng ở trang nhập hàng</span>
                            @else
                                @forelse($role->permissions as $permission)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <span class="text-gray-400 italic">Chưa cấp quyền nào</span>
                                @endforelse
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            @can('update', $role)
                                <a href="{{ route('roles.edit', $role->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md">Sửa</a>
                            @endcan
                            
                            @can('delete', $role)
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vai trò này?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-md">Xóa</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Không có dữ liệu vai trò.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection