@extends('layouts.admin')

@section('content')
    @php
        // Chỉ hiện nút create nếu có quyền
        $createRoute = auth()->user()->can('create', App\Models\User::class) 
            ? route('users.create') 
            : null;
    @endphp

    <x-crud.index 
        title="Quản lý Người dùng" 
        :createRoute="$createRoute" 
        :data="$users"
    >
        {{-- FILTER --}}
        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('users.index') }}">
                
                {{-- Search --}}
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm tên, email, username..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                {{-- Status --}}
                <select name="status" class="block w-full md:w-40 py-2 pl-3 pr-10 border border-gray-300 rounded-lg sm:text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Bị khóa</option>
                </select>

                {{-- Role --}}
                <select name="role" class="block w-full md:w-40 py-2 pl-3 pr-10 border border-gray-300 rounded-lg sm:text-sm">
                    <option value="">Tất cả role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Active --}}
                <input type="checkbox" name="include_inactive" value="1" {{ request('include_inactive') ? 'checked' : '' }} class="ml-2">
                <span class="text-sm text-gray-600">Bao gồm người dùng đã xóa</span>

                {{-- Giữ sort --}}
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request('dir')) <input type="hidden" name="dir" value="{{ request('dir') }}"> @endif

                {{-- Per page --}}
                <x-slot name="perPage">
                    <div class="flex items-center text-sm text-gray-500">
                        <span class="mr-2">Hiển thị:</span>
                        <select name="per_page" onchange="this.form.submit()"
                            class="py-1.5 pl-3 pr-8 border border-gray-300 rounded-lg text-sm">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </x-slot>

            </x-ui.filter-bar>
        </x-slot>

        {{-- TABLE --}}
        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column sortable="true" name="id">ID</x-ui.table.column>
                <x-ui.table.column sortable="true" name="full_name">Người dùng</x-ui.table.column>
                <x-ui.table.column sortable="true" name="username">Tài khoản</x-ui.table.column>
                <x-ui.table.column sortable="true" name="is_active" align="center">Trạng thái</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($users as $user)
                <tr class=" {{ $user->trashed() ? 'bg-red-100 hover:bg-red-200' : 'hover:bg-gray-50' }}">
                    {{-- ID --}}
                    <td class="px-6 py-4 font-medium">#{{ $user->id }}</td>

                    {{-- User --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full"
                                src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=E0E7FF&color=4338CA">
                            <div class="ml-4">
                                <div class="text-sm font-medium">{{ $user->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Username + Role --}}
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->username }}
                        @if($user->roles->count())
                            <div class="text-xs text-indigo-600 mt-1 font-semibold">
                                {{ $user->roles->first()->name }}
                            </div>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-6 py-4 text-center">
                        @if($user->is_active)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Hoạt động</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Đã khóa</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">

                            @if($user->trashed())
                                @can('delete', $user)
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 bg-green-50 px-3 py-1.5 rounded-md">Khôi phục</button>
                                    </form>
                                @endcan
                            @else
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user->id) }}"
                                    class="text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-md">
                                        Sửa
                                    </a>
                                @endcan
                            @endif

                            @if($user->trashed())
                                @can('delete', $user)
                                    <form action="{{ route('users.force-delete', $user->id) }}" method="POST" class="inline" 
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn người dùng này?');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 bg-red-50 px-3 py-1.5 rounded-md">Xóa</button>
                                    </form>
                                @endcan
                            @else
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Chuyển người dùng này vào thùng rác?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 bg-red-50 px-3 py-1.5 rounded-md">
                                            Xóa
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">
                        Không có người dùng nào
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection
