@extends('layouts.admin')

@section('content')
    <x-crud.index 
        title="Quản lý Giao dịch Thanh toán" 
        createRoute="" 
        :data="$payments"
    >
        <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm mb-4 border border-blue-200">
            <p><i class="fa-solid fa-shield-halved"></i> <strong>Chế độ Bảo mật:</strong> Hệ thống tự động chặn tính năng XÓA và TẠO MỚI thanh toán từ Admin để đảm bảo đối soát kế toán. Admin chỉ được phép đổi trạng thái giao dịch.</p>
        </div>

        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('payments.index') }}">
                <div class="relative w-full md:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm Mã giao dịch hoặc Mã Đơn..." class="block w-full px-3 py-2 border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500">
                </div>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">Mã GD</x-ui.table.column>
                <x-ui.table.column name="order">Đơn hàng tham chiếu</x-ui.table.column>
                <x-ui.table.column name="method">Phương thức</x-ui.table.column>
                <x-ui.table.column name="amount">Số tiền</x-ui.table.column>
                <x-ui.table.column name="status" align="center">Trạng thái</x-ui.table.column>
                <x-ui.table.column align="right">Cập nhật Trạng thái</x-ui.table.column>
            </x-slot>

            @forelse($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold text-gray-900">#PAY-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-indigo-600">ĐH #{{ str_pad($payment->order_id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 text-sm font-medium uppercase text-gray-700">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-red-600">{{ number_format($payment->amount) }} đ</td>
                    <td class="px-6 py-4 text-center">
                        @if($payment->status === 'paid')
                            <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Đã thanh toán</span>
                        @elseif($payment->status === 'failed')
                            <span class="px-2 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full">Thất bại</span>
                        @else
                            <span class="px-2 py-1 text-xs font-bold bg-yellow-100 text-yellow-800 rounded-full">Chờ thanh toán</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <!-- Form Inline Edit Trạng thái -->
                        <form action="{{ route('payments.update', $payment->id) }}" method="POST" class="flex justify-end space-x-2">
                            @csrf @method('PUT')
                            <select name="status" class="text-sm border-gray-300 rounded-md py-1 pl-2 pr-6 shadow-sm focus:ring-indigo-500">
                                <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="paid" {{ $payment->status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>Thất bại</option>
                            </select>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition" onclick="return confirm('Bạn xác nhận thay đổi trạng thái giao dịch này?');">Lưu</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-500">Không có giao dịch thanh toán nào.</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection