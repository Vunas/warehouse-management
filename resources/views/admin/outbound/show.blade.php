@extends('layouts.admin')

@section('title', 'Chi tiết Xuất kho')
@section('header', 'Phiếu: #OUT-' . str_pad($outboundTicket->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">Thông tin Phiếu</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Trạng thái:</span>
                    @if($outboundTicket->status == 'pending')
                        <span class="bg-yellow-100 text-yellow-700 px-2 rounded font-bold text-xs">Chờ xử lý</span>
                    @elseif($outboundTicket->status == 'completed')
                        <span class="bg-green-100 text-green-700 px-2 rounded font-bold text-xs">Hoàn tất</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Ngày yêu cầu:</span>
                    <span class="font-medium">{{ $outboundTicket->requested_date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Khách hàng:</span>
                    <span class="font-medium">{{ $outboundTicket->contract->customer->company_name }}</span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100">
                @if($outboundTicket->status == 'pending')
                    <p class="text-xs text-gray-500 mb-2 italic">Hệ thống sẽ tự động trừ tồn kho theo nguyên tắc FIFO (Nhập trước xuất trước).</p>
                    @can('outbound.process')
                    <form action="{{ route('outbound_tickets.process', $outboundTicket->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-orange-700 shadow-sm flex justify-center items-center">
                            <i class="fa-solid fa-dolly mr-2"></i> Xuất Kho & Trừ Tồn
                        </button>
                    </form>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800">Danh sách Hàng Xuất</h3>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-3">Sản phẩm</th>
                    <th class="px-6 py-3">Mã SKU</th>
                    <th class="px-6 py-3 text-center">Số lượng Yêu cầu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($outboundTicket->details as $detail)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $detail->product->name }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500 font-mono">{{ $detail->product->sku }}</td>
                    <td class="px-6 py-4 text-center font-bold text-lg text-orange-600">{{ $detail->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection