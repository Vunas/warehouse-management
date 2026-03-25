@extends('layouts.admin')

@section('content')
    <x-crud.index 
        title="Quản lý Tồn kho" 
        createRoute="{{ route('inventory.create') }}" 
        :data="$inventories"
    >
        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">ID</x-ui.table.column>
                <x-ui.table.column name="product">Sản phẩm</x-ui.table.column>
                <x-ui.table.column name="location">Vị trí (Kho - Vị trí)</x-ui.table.column>
                <x-ui.table.column name="quantity" align="center">Số lượng tồn</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($inventories as $inv)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ $inv->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        {{ $inv->product->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <span class="font-semibold text-indigo-600">{{ $inv->location->warehouse->name ?? 'N/A' }}</span>
                        <br>
                        <span class="text-xs text-gray-500">Vị trí: {{ $inv->location->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold {{ $inv->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $inv->quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('inventory.show', $inv->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1.5 rounded-md">Xem</a>
                            <a href="{{ route('inventory.edit', $inv->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md">Sửa</a>
                            
                            <form action="{{ route('inventory.destroy', $inv->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tồn kho này?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-md">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">Không có dữ liệu tồn kho.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection