@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Thông tin phiếu -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex justify-between items-start">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Phiếu Xuất Kho: OUT-{{ str_pad($outbound->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <p class="text-sm text-gray-500 mt-2">
                Tham chiếu Đơn hàng: <a href="#" class="font-bold text-indigo-600 hover:underline">ĐH #{{ str_pad($outbound->order_id, 5, '0', STR_PAD_LEFT) }}</a>
            </p>
            <p class="text-sm text-gray-500 mt-1">Người lập phiếu: {{ $outbound->staff->full_name ?? 'N/A' }} | Ngày lập: {{ $outbound->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            @if($outbound->status === 'pending') <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-full font-bold">Chờ xuất kho</span>
            @elseif($outbound->status === 'completed') <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full font-bold">Đã hoàn tất xuất kho</span>
            @else <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full font-bold">Đã hủy</span> @endif
        </div>
    </div>

    <!-- Bảng danh sách hàng cần xuất -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-6">
        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Danh sách Sản phẩm Cần xuất (Pick List)</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Số lượng cần lấy</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($outbound->items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $item->product->name ?? 'N/A' }}</td>
                            <td class="px-4 py-4 text-sm text-center font-bold text-indigo-600 text-lg">{{ $item->quantity }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-8 text-center text-gray-500 italic">Phiếu xuất này không có sản phẩm nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($outbound->status === 'pending')
            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                <form action="{{ route('outbounds.cancel', $outbound->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white border border-red-300 text-red-700 px-4 py-2 rounded-lg font-medium hover:bg-red-50" onclick="return confirm('Bạn chắc chắn muốn hủy phiếu xuất này?');">Hủy Phiếu</button>
                </form>
                
                <form action="{{ route('outbounds.complete', $outbound->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-green-700 shadow-md" onclick="return confirm('Hệ thống sẽ tự động tìm các lô hàng cũ nhất (FIFO) để trừ tồn kho. Bạn có chắc chắn muốn xác nhận xuất kho?');">
                        ✔ HOÀN TẤT & TRỪ KHO (FIFO)
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection