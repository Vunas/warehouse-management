@extends('layouts.admin')

@section('content')
    @php
        $createRoute = auth()->user()->can('create', App\Models\Warehouse::class) 
            ? route('warehouses.create') 
            : null;
    @endphp

    <x-crud.index title="Quản lý Kho bãi" :createRoute="$createRoute" :data="$warehouses">
        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">Mã Kho</x-ui.table.column>
                <x-ui.table.column name="name">Tên Kho</x-ui.table.column>
                <x-ui.table.column name="location">Địa chỉ / Vị trí</x-ui.table.column>
                <x-ui.table.column name="created_at">Ngày tạo</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($warehouses as $warehouse)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold text-gray-700">WH-{{ str_pad($warehouse->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 font-semibold text-indigo-700">
                        <i class="fa-solid fa-warehouse mr-2 text-gray-400"></i> {{ $warehouse->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $warehouse->location }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $warehouse->created_at ? $warehouse->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') : '' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            @can('update', $warehouse)
                                <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-md hover:bg-indigo-100">Sửa</a>
                            @endcan
                            @can('delete', $warehouse)
                                <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" onsubmit="return confirm('Xóa kho này sẽ ảnh hưởng tới các khu vực/kệ bên trong. Bạn chắc chắn chứ?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 bg-red-50 px-3 py-1.5 rounded-md hover:bg-red-100">Xóa</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-10 text-gray-500">Chưa có dữ liệu kho bãi</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection