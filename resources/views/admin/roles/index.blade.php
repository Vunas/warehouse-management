@extends('layouts.admin')

@section('content')
    @php
        $createRoute = auth()->user()->can('create', Spatie\Permission\Models\Role::class) ? route('roles.create') : '';
    @endphp

    <x-crud.index title="Quản lý Vai trò (Roles)" :createRoute="$createRoute" :data="$roles">
        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('roles.index') }}">
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên vai trò..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors shadow-sm">
                </div>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id" class="w-16">ID</x-ui.table.column>
                <x-ui.table.column name="name" class="w-48">Tên Vai trò</x-ui.table.column>
                <x-ui.table.column name="permissions">Quyền hạn (Permissions)</x-ui.table.column>
                <x-ui.table.column align="right" class="w-32">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($roles as $role)
                <tr class="hover:bg-indigo-50/30 transition-colors group border-b border-gray-100 last:border-0">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">#{{ $role->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-sm font-bold bg-gray-100 text-gray-800 border border-gray-200 shadow-sm">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            {{ strtoupper($role->name) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex flex-wrap gap-1.5">
                            @if ($role->name === 'admin')
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                    <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Toàn quyền hệ thống (Super Admin)
                                </span>
                            @elseif($role->name === 'customer')
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Tài khoản Khách hàng
                                </span>
                            @else
                                @php
                                    $rolePerms = $role->permissions;
                                    $displayCount = 5;
                                @endphp

                                @forelse($rolePerms->take($displayCount) as $permission)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <span
                                        class="text-gray-400 italic text-xs bg-gray-50 px-2 py-1 rounded-md border border-dashed border-gray-200">Chưa
                                        cấp quyền nào</span>
                                @endforelse

                                @if ($rolePerms->count() > $displayCount)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200 cursor-help"
                                        title="Xem chi tiết trong phần Sửa">
                                        +{{ $rolePerms->count() - $displayCount }} quyền khác
                                    </span>
                                @endif
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div
                            class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            @can('update', $role)
                                <a href="{{ route('roles.edit', $role->id) }}"
                                    class="inline-flex items-center justify-center text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition-colors">
                                    Sửa
                                </a>
                            @endcan

                            @can('delete', $role)
                                @if ($role->name !== 'admin')
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa vai trò này? Cảnh báo: Các user đang giữ vai trò này có thể bị mất quyền!');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                                            Xóa
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-12 h-12 text-gray-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-base font-medium">Chưa có vai trò nào được tạo.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection
