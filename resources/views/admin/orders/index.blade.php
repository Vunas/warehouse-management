@extends('layouts.admin')

@section('content')
    <x-crud.index title="Quản lý Đơn đặt hàng" :data="$orders">
        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('orders.index') }}">
                <select name="status" class="block w-full md:w-48 py-2 pl-3 pr-10 border border-gray-300 rounded-lg sm:text-sm font-medium text-gray-700" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Mới tạo</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý kho</option>
                    <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">Mã Đơn</x-ui.table.column>
                <x-ui.table.column name="user">Khách hàng</x-ui.table.column>
                <x-ui.table.column name="total">Tổng tiền</x-ui.table.column>
                <x-ui.table.column name="status" align="center">Trạng thái</x-ui.table.column>
                <x-ui.table.column name="date">Ngày đặt</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold text-indigo-700">#ORD-{{ $order->id }}</td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $order->user->full_name ?? 'Khách vô danh' }}</div>
                        <div class="text-xs text-gray-500">{{ $order->user->phone ?? '' }}</div>
                    </td>

                    <td class="px-6 py-4 text-sm font-black text-red-600">
                        {{ number_format($order->total_price, 0, ',', '.') }} đ
                    </td>

                    <td class="px-6 py-4 text-center">
                        @php
                            $badgeColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'processing' => 'bg-indigo-100 text-indigo-800',
                                'shipping' => 'bg-purple-100 text-purple-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = [
                                'pending' => 'Mới tạo', 'confirmed' => 'Đã xác nhận',
                                'processing' => 'Đang xử lý', 'shipping' => 'Đang giao',
                                'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $badgeColors[$order->status] ?? 'bg-gray-100' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-sm font-medium text-gray-600">
                        {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}
                    </td>

                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center px-4 py-1.5 bg-white border border-gray-300 text-gray-800 rounded-md font-medium text-sm hover:bg-gray-50 shadow-sm transition">
                            Kiểm tra
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-10 text-gray-500">Không có đơn hàng nào</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection