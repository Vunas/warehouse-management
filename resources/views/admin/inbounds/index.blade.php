@extends('layouts.admin')

@section('content')
    <x-crud.index title="Quản lý Phiếu Nhập Kho" :createRoute="route('inbounds.create')" :data="$inbounds">
        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">Mã Phiếu</x-ui.table.column>
                <x-ui.table.column name="supplier">Nhà cung cấp</x-ui.table.column>
                <x-ui.table.column name="staff">Nhân viên lập</x-ui.table.column>
                <x-ui.table.column name="status" align="center">Trạng thái</x-ui.table.column>
                <x-ui.table.column name="date">Ngày lập</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($inbounds as $inbound)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold text-indigo-700">INB-{{ str_pad($inbound->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $inbound->supplier->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $inbound->staff->full_name ?? 'N/A' }}</td>
                    
                    <td class="px-6 py-4 text-center">
                        @if($inbound->status === 'pending')
                            <span class="px-2 py-1 text-xs font-bold bg-yellow-100 text-yellow-800 rounded-full">Chờ xử lý</span>
                        @elseif($inbound->status === 'completed')
                            <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Đã nhập kho</span>
                        @else
                            <span class="px-2 py-1 text-xs font-bold bg-red-100 text-red-800 rounded-full">Đã hủy</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">{{ $inbound->created_at->format('d/m/Y H:i') }}</td>
                    
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('inbounds.show', $inbound->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md font-medium text-sm transition">
                            Chi tiết & Xử lý
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-10 text-gray-500">Chưa có phiếu nhập kho nào</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection