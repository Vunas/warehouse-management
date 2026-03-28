@extends('layouts.admin')

@section('content')
    <x-crud.index title="Quản lý Thương hiệu" createRoute="{{ route('brands.create') }}" :data="$brands">
        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('brands.index') }}">
                <div class="relative w-full md:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm thương hiệu..." class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">ID</x-ui.table.column>
                <x-ui.table.column name="name">Tên Thương hiệu</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($brands as $brand)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold text-gray-900">#{{ $brand->id }}</td>
                    <td class="px-6 py-4 font-medium text-indigo-600">{{ $brand->name }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('brands.edit', $brand->id) }}" class="text-indigo-600 bg-indigo-50 px-3 py-1 rounded">Sửa</a>
                            <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 bg-red-50 px-3 py-1 rounded">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center py-8 text-gray-500">Chưa có thương hiệu nào</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection