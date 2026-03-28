@extends('layouts.admin')

@section('content')
    <x-crud.index 
        title="Quản lý Phiếu Xuất Kho" 
        createRoute="{{ route('outbounds.create') }}" 
        :data="$outbounds"
    >
        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">Mã Phiếu</x-ui.table.column>
                <x-ui.table.column name="order">Mã Đơn Hàng</x-ui.table.column>
                <x-ui.table.column name="staff">Nhân viên lập</x-ui.table.column>
                <x-ui.table.column name="status" align="center">Trạng thái</x-ui.table.column>
                <x-ui.table.column name="date">Ngày lập</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($outbounds as $outbound)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-700">OUT-{{ str_pad($outbound->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ĐH #{{ str_pad($outbound->order_id, 5, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $outbound->staff->full_name ?? 'N/A' }}</td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($outbound->status === 'pending')
                            <span class="px-2 py-1 text-xs font-bold bg-yellow-100 text-yellow-800 rounded-full">Chờ xử lý</span>
                        @elseif($outbound->status === 'completed')
                            <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Đã xuất kho</span>
                        @else
                            <span class="px-2 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full">Đã hủy</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $outbound->created_at->format('d/m/Y H:i') }}</td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('outbounds.show', $outbound->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md font-medium text-sm transition">
                            Chi tiết & Xuất kho
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có phiếu xuất kho nào.</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection