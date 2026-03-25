@extends('layouts.admin')

@section('content')
    <x-crud.index 
        title="Quản lý Nhà Cung Cấp" 
        createRoute="{{ route('suppliers.create') }}" 
        :data="$suppliers"
    >
        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('suppliers.index') }}">
                <div class="relative w-full md:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, số điện thoại..." class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">ID</x-ui.table.column>
                <x-ui.table.column name="name">Tên Nhà Cung Cấp</x-ui.table.column>
                <x-ui.table.column name="phone">Số Điện Thoại</x-ui.table.column>
                <x-ui.table.column name="email">Email</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($suppliers as $supplier)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">#{{ $supplier->id }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-indigo-700">{{ $supplier->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $supplier->phone ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $supplier->email ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition">Sửa</a>
                            <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có nhà cung cấp nào trong hệ thống.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection