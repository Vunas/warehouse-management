@extends('layouts.admin')

@section('content')
    <x-crud.index 
        title="Theo dõi Giỏ hàng Khách hàng (Cart Abandonment)" 
        createRoute="" 
        :data="$carts"
    >
        <div class="bg-indigo-50 text-indigo-800 p-4 rounded-lg text-sm mb-4 border border-indigo-200">
            <p>Đây là khu vực theo dõi các sản phẩm đang nằm trong giỏ hàng của Khách Hàng (Chưa chốt đơn). Hệ thống <strong>cấm Admin can thiệp (Thêm/Sửa/Xóa)</strong> để đảm bảo tính riêng tư của giỏ hàng khách.</p>
        </div>

        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('carts.index') }}">
                <div class="relative w-full md:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo Tên hoặc Email khách..." class="block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500">
                </div>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="user">Khách hàng</x-ui.table.column>
                <x-ui.table.column name="product">Sản phẩm trong giỏ</x-ui.table.column>
                <x-ui.table.column name="quantity" align="center">Số lượng</x-ui.table.column>
                <x-ui.table.column name="price" align="right">Tổng tạm tính</x-ui.table.column>
                <x-ui.table.column name="date" align="right">Bỏ vào giỏ lúc</x-ui.table.column>
            </x-slot>

            @forelse($carts as $cart)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $cart->user->full_name ?? 'Khách vãng lai' }}</div>
                        <div class="text-xs text-gray-500">{{ $cart->user->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-indigo-600">{{ $cart->product->name ?? 'Sản phẩm đã xóa' }}</td>
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-800">{{ $cart->quantity }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-red-600">
                        {{ number_format(($cart->product->price ?? 0) * $cart->quantity) }} đ
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                        {{ $cart->updated_at->diffForHumans() }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-8 text-gray-500">Hiện tại không có giỏ hàng nào.</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection