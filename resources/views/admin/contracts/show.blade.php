@extends('layouts.admin')

@section('title', 'Chi tiết Hợp đồng')
@section('header', 'Hợp đồng: ' . $contract->contract_code)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Thông tin chính -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">Thông tin chung</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Trạng thái:</span>
                    <span class="font-bold uppercase {{ $contract->status == 'active' ? 'text-green-600' : 'text-red-600' }}">{{ $contract->status }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Khách hàng:</span>
                    <span class="font-medium text-right">{{ $contract->customer->company_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Ngày bắt đầu:</span>
                    <span>{{ $contract->start_date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Ngày kết thúc:</span>
                    <span>{{ $contract->end_date->format('d/m/Y') }}</span>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t">
                @can('update', $contract)
                <a href="{{ route('contracts.edit', $contract->id) }}" class="block w-full bg-gray-100 text-gray-700 text-center py-2 rounded text-sm hover:bg-gray-200">
                    <i class="fa-solid fa-pen mr-1"></i> Chỉnh sửa / Gia hạn
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Danh sách Lô thuê -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800">Các Lô/Kệ đang thuê</h3>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-white text-gray-500 border-b">
                    <tr>
                        <th class="px-6 py-3">Mã Lô</th>
                        <th class="px-6 py-3">Kho</th>
                        <th class="px-6 py-3">Sức chứa cam kết</th>
                        <th class="px-6 py-3">Đơn giá (VNĐ)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($contract->contractBlocks as $cb)
                    <tr>
                        <td class="px-6 py-4 font-mono text-blue-600 font-bold">{{ $cb->storageBlock->block_code }}</td>
                        <td class="px-6 py-4">{{ $cb->storageBlock->warehouse->name }}</td>
                        <td class="px-6 py-4">{{ $cb->slots_committed }} slots</td>
                        <td class="px-6 py-4">{{ number_format($cb->rental_price) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Lịch sử Nhập/Xuất -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded shadow-sm">
                <div class="text-xs text-gray-500 uppercase mb-1">Tổng phiếu nhập</div>
                <div class="text-2xl font-bold">{{ $contract->inboundTickets->count() }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow-sm">
                <div class="text-xs text-gray-500 uppercase mb-1">Tổng phiếu xuất</div>
                <div class="text-2xl font-bold">{{ $contract->outboundTickets->count() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection