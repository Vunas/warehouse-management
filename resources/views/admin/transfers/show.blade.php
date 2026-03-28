@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Phiếu -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex justify-between items-start">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Phiếu Luân chuyển: #TRF-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <div class="mt-2 text-sm text-gray-600 flex items-center space-x-2">
                <span class="font-bold text-red-600 bg-red-50 px-2 py-1 rounded">XUẤT: {{ $transfer->fromLocation->name ?? 'N/A' }}</span>
                <i class="fa-solid fa-arrow-right text-gray-400"></i>
                <span class="font-bold text-green-600 bg-green-50 px-2 py-1 rounded">NHẬP: {{ $transfer->toLocation->name ?? 'N/A' }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-2">Người lập: {{ $transfer->staff->full_name ?? 'N/A' }} | Ngày lập: {{ $transfer->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            @if($transfer->status === 'pending') <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-full font-semibold">Chờ xử lý (Bản nháp)</span>
            @elseif($transfer->status === 'completed') <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full font-semibold">Đã hoàn tất</span>
            @else <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full font-semibold">Đã hủy</span> @endif
        </div>
    </div>

    @if($transfer->status === 'pending')
    <!-- Section 1: Thêm sản phẩm -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">1. Thêm sản phẩm cần chuyển</h3>
        <form action="{{ route('transfers.items.add', $transfer->id) }}" method="POST" class="flex gap-4 items-end flex-wrap">
            @csrf
            <div class="flex-1 min-w-[300px]">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn Hàng tồn tại vị trí xuất *</label>
                <select name="inventory_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                    <option value="">-- Chọn sản phẩm tồn kho --</option>
                    @foreach($inventories as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->product->name }} (Đang tồn: {{ $inv->quantity }})</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Số lượng chuyển *</label>
                <input type="number" name="quantity" min="1" value="1" required class="block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">Thêm vào phiếu</button>
        </form>
    </div>
    @endif

    <!-- Section 2: Danh sách chi tiết luân chuyển -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-6">
        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">2. Danh sách Sản phẩm luân chuyển</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Số lượng chuyển</th>
                        @if($transfer->status === 'pending') <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th> @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transfer->items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $item->product->name ?? 'N/A' }}</td>
                        
                        @if($transfer->status === 'pending')
                            <!-- Form sửa số lượng trực tiếp -->
                            <td class="px-4 py-4">
                                <input type="number" name="quantity" value="{{ $item->quantity }}" form="update-{{$item->id}}" required min="1" class="w-24 border-gray-300 rounded text-center text-sm shadow-sm focus:ring-indigo-500 mx-auto block">
                            </td>
                            <td class="px-4 py-4 text-right space-x-2">
                                <button type="submit" form="update-{{$item->id}}" class="text-blue-600 hover:text-blue-900 font-bold text-sm bg-blue-50 px-2 py-1 rounded">Lưu</button>
                                <button type="submit" form="del-{{$item->id}}" class="text-red-600 hover:text-red-900 font-bold text-sm bg-red-50 px-2 py-1 rounded" onclick="return confirm('Xóa SP khỏi phiếu?');">Xóa</button>
                            </td>
                        @else
                            <td class="px-4 py-4 text-sm text-center font-bold text-indigo-600">{{ $item->quantity }}</td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ $transfer->status === 'pending' ? '3' : '2' }}" class="px-4 py-8 text-center text-gray-500 italic">Chưa có sản phẩm nào trong phiếu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfer->status === 'pending' && count($transfer->items ?? []) > 0)
            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="submit" form="cancel-form" class="bg-white border border-red-300 text-red-700 px-4 py-2 rounded-lg font-medium hover:bg-red-50" onclick="return confirm('Bạn chắc chắn muốn hủy bỏ toàn bộ phiếu điều chuyển này?');">Hủy Phiếu</button>
                
                <button type="submit" form="complete-form" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-green-700 shadow-md" onclick="return confirm('Kho xuất sẽ tự động bị trừ, kho nhập sẽ tự động được cộng tồn kho. Xác nhận luân chuyển?');">
                    ✔ HOÀN TẤT LUÂN CHUYỂN
                </button>
            </div>
        @endif

        <!-- ================= CÁC FORM NGẦM ĐỂ XỬ LÝ DỮ LIỆU ================= -->
        <form id="complete-form" action="{{ route('transfers.complete', $transfer->id) }}" method="POST" class="hidden">@csrf</form>
        <form id="cancel-form" action="{{ route('transfers.cancel', $transfer->id) }}" method="POST" class="hidden">@csrf</form>
        
        @if($transfer->status === 'pending')
            @foreach($transfer->items as $item)
                <form id="update-{{$item->id}}" action="{{ route('transfers.items.update', [$transfer->id, $item->id]) }}" method="POST" class="hidden">@csrf @method('PUT')</form>
                <form id="del-{{$item->id}}" action="{{ route('transfers.items.remove', [$transfer->id, $item->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
            @endforeach
        @endif
    </div>
</div>
@endsection