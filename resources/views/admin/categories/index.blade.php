@extends('layouts.admin')

@section('content')
    @php
        $createRoute = auth()->user()->can('create', App\Models\Category::class) 
            ? route('categories.create') 
            : null;
    @endphp

    <x-crud.index title="Quản lý Danh mục" :createRoute="$createRoute" :data="$categories">
        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">ID</x-ui.table.column>
                <x-ui.table.column name="name">Tên danh mục</x-ui.table.column>
                <x-ui.table.column name="created_at">Ngày tạo</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $category->id }}</td>
                    <td class="px-6 py-4 font-semibold text-indigo-600">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $category->created_at ? $category->created_at->format('d/m/Y') : '' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            @can('update', $category)
                                <a href="{{ route('categories.edit', $category->id) }}" class="text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-md hover:bg-indigo-100">Sửa</a>
                            @endcan
                            @can('delete', $category)
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Xóa danh mục này?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 bg-red-50 px-3 py-1.5 rounded-md hover:bg-red-100">Xóa</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-10 text-gray-500">Chưa có danh mục nào</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection