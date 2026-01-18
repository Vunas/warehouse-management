@extends('layouts.admin')

@section('title', 'Chi tiết Nhập kho')
@section('header', 'Phiếu: #IN-' . str_pad($inboundTicket->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Thông tin chung -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">Thông tin Phiếu</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Trạng thái:</span>
                    @if($inboundTicket->status == 'pending')
                        <span class="bg-yellow-100 text-yellow-700 px-2 rounded font-bold text-xs">Chờ duyệt</span>
                    @elseif($inboundTicket->status == 'approved')
                        <span class="bg-blue-100 text-blue-700 px-2 rounded font-bold text-xs">Đã duyệt (Chờ hàng)</span>
                    @elseif($inboundTicket->status == 'received')
                        <span class="bg-green-100 text-green-700 px-2 rounded font-bold text-xs">Hoàn tất</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Dự kiến:</span>
                    <span class="font-medium">{{ $inboundTicket->expected_date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Khách hàng:</span>
                    <span class="font-medium">{{ $inboundTicket->contract->customer->company_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Hợp đồng:</span>
                    <span class="font-mono text-blue-600">{{ $inboundTicket->contract->contract_code }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 space-y-2 pt-4 border-t border-gray-100">
                @if($inboundTicket->status == 'pending')
                    <p class="text-xs text-gray-500 mb-2 italic">Hệ thống sẽ tự động tính toán kích thước và quy đổi ra slot.</p>
                    @can('inbound.approve')
                    <form action="{{ route('inbound_tickets.approve', $inboundTicket->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm flex justify-center items-center">
                            <i class="fa-solid fa-calculator mr-2"></i> Tính toán Slot & Duyệt
                        </button>
                    </form>
                    @endcan
                @endif

                @if($inboundTicket->status == 'approved')
                    <p class="text-xs text-gray-500 mb-2 italic">Xác nhận khi hàng thực tế đã đến kho.</p>
                    @can('inbound.process')
                    <form action="{{ route('inbound_tickets.process', $inboundTicket->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-green-700 shadow-sm flex justify-center items-center">
                            <i class="fa-solid fa-check-double mr-2"></i> Xác nhận Nhập kho
                        </button>
                    </form>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <!-- Danh sách hàng hóa -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Chi tiết Hàng hóa</h3>
            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">{{ $inboundTicket->details->count() }} items</span>
        </div>
        
        <table class="w-full text-left text-sm">
            <thead class="bg-white text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-3">Sản phẩm</th>
                    <th class="px-6 py-3 text-center">Số lượng</th>
                    <th class="px-6 py-3">Kích thước nhập (m)</th>
                    <th class="px-6 py-3">Quy đổi (Kết quả)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($inboundTicket->details as $detail)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $detail->product->name }}</div>
                        <div class="text-xs text-gray-500">{{ $detail->product->sku }}</div>
                    </td>
                    <td class="px-6 py-4 text-center font-bold">
                        {{ $detail->quantity }}
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-600">
                        {{ $detail->input_length }} x {{ $detail->input_width }} x {{ $detail->input_height }}
                    </td>
                    <td class="px-6 py-4">
                        @if($detail->calculatedSlot)
                            <div class="text-xs">
                                @if($detail->calculatedSlot->is_violation)
                                    <span class="text-red-600 font-bold"><i class="fa-solid fa-triangle-exclamation"></i> Vi phạm KT</span>
                                @else
                                    <span class="text-green-600 font-bold"><i class="fa-solid fa-check"></i> Hợp lệ</span>
                                @endif
                                <div class="mt-1">
                                    Chiếm: <b>{{ $detail->calculatedSlot->final_slot_cost }} slots</b> / item
                                </div>
                                <div class="text-blue-600 font-bold mt-1">
                                    Tổng: {{ $detail->calculatedSlot->final_slot_cost * $detail->quantity }} slots
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400 italic text-xs">Chưa tính toán</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection