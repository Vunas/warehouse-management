@extends('layouts.admin')

@section('content')
    <x-crud.index 
        title="Quản lý Người dùng" 
        createRoute="{{ route('users.create') }}" 
        :data="$users"
    >
        <x-slot name="filters">
            <!-- Filter Bar Component -->
            <x-ui.filter-bar action="{{ route('users.index') }}">
                
                <!-- Search Input -->
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, email, username..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                </div>

                <!-- Status Select -->
                <select name="status" class="block w-full md:w-40 py-2 pl-3 pr-10 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Bị khóa</option>
                </select>

                <!-- Giữ nguyên Sort khi Lọc -->
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request('dir')) <input type="hidden" name="dir" value="{{ request('dir') }}"> @endif

                <!-- Per Page Slot -->
                <x-slot name="perPage">
                    <div class="flex items-center text-sm text-gray-500 w-full sm:w-auto mt-4 sm:mt-0">
                        <span class="mr-2 whitespace-nowrap">Hiển thị:</span>
                        <select name="per_page" onchange="this.form.submit()" class="py-1.5 pl-3 pr-8 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 dòng</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 dòng</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 dòng</option>
                        </select>
                    </div>
                </x-slot>

            </x-ui.filter-bar>
        </x-slot>

        <!-- Gọi Table Component -->
        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column sortable="true" name="id">ID</x-ui.table.column>
                <x-ui.table.column sortable="true" name="full_name">Người dùng</x-ui.table.column>
                <x-ui.table.column sortable="true" name="username">Tài khoản</x-ui.table.column>
                <x-ui.table.column sortable="true" name="is_active" align="center">Trạng thái</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ $user->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 shrink-0">
                                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=E0E7FF&color=4338CA" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $user->username }}
                        @if($user->roles->count())
                            <div class="text-xs text-indigo-600 mt-1">{{ $user->roles->first()->name }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hoạt động</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Đã khóa</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md transition-colors">Sửa</a>
                            
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Chuyển người dùng này vào thùng rác?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-md transition-colors">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không có người dùng nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Thử thay đổi bộ lọc hoặc thêm người dùng mới.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection