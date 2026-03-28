@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Thông tin phiếu -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex justify-between items-start">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Phiếu Nhập: INB-{{ str_pad($inbound->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <p class="text-sm text-gray-500 mt-1">Nhà cung cấp: <span class="font-semibold">{{ $inbound->supplier->name ?? 'N/A' }}</span></p>
            <p class="text-sm text-gray-500">Người lập: {{ $inbound->staff->full_name ?? 'N/A' }} | Ngày lập: {{ $inbound->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            @if($inbound->status === 'pending') <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-full font-semibold">Chờ xử lý (Bản nháp)</span>
            @elseif($inbound->status === 'completed') <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full font-semibold">Đã hoàn tất</span>
            @else <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full font-semibold">Đã hủy</span> @endif
        </div>
    </div>

    @if($inbound->status === 'pending')
    <!-- Form thêm sản phẩm nhập -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">1. Thêm sản phẩm vào phiếu nháp</h3>
        <form action="{{ route('inbounds.addItem', $inbound->id) }}" method="POST" class="flex gap-4 items-end flex-wrap">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sản phẩm</label>
                <select name="product_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Số lượng</label>
                <input type="number" name="quantity" min="1" value="1" required class="block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="w-40">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Đơn giá nhập</label>
                <input type="number" name="price" min="0" value="0" required class="block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">Thêm SP</button>
        </form>
    </div>
    @endif

    <!-- Bảng sản phẩm chi tiết -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-6">
        <h3 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">
            2. Chi tiết hàng hóa & Chỉ định Vị trí lưu trữ
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Đơn giá</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chỉ định Vị trí lưu trữ</th>
                        @if($inbound->status === 'pending') <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th> @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($inbound->items ?? [] as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $item->product->name ?? 'N/A' }}</td>
                            
                            @if($inbound->status === 'pending')
                                <!-- Form sửa Số lượng và Giá trực tiếp -->
                                <td class="px-4 py-4">
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" form="update-{{$item->id}}" required min="1" class="w-20 border-gray-300 rounded text-center text-sm shadow-sm focus:ring-indigo-500 mx-auto block">
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <input type="number" name="price" value="{{ round($item->price) }}" form="update-{{$item->id}}" required min="0" class="w-28 border-gray-300 rounded text-right text-sm shadow-sm focus:ring-indigo-500 ml-auto block">
                                </td>
                                <td class="px-4 py-4">
                                    <!-- Input Location gắn với form Hoàn tất -->
                                    <select name="location_assignments[{{ $item->id }}]" form="complete-form" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                                        <option value="">-- Chọn Vị trí --</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->warehouse->name ?? '' }} - {{ $location->name }} ({{ strtoupper($location->type) }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-4 text-right space-x-2">
                                    <!-- Nút Lưu gắn với form Update -->
                                    <button type="submit" form="update-{{$item->id}}" class="text-blue-600 hover:text-blue-900 font-bold text-sm bg-blue-50 px-2 py-1 rounded">Lưu</button>
                                    <!-- Nút Xóa gắn với form Delete -->
                                    <button type="submit" form="del-{{$item->id}}" class="text-red-600 hover:text-red-900 font-bold text-sm bg-red-50 px-2 py-1 rounded" onclick="return confirm('Xóa SP khỏi phiếu?');">Xóa</button>
                                </td>
                            @else
                                <!-- Trạng thái đã hoàn tất (Chỉ xem) -->
                                <td class="px-4 py-4 text-sm text-center font-bold text-indigo-600">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 text-sm text-right">{{ number_format($item->price) }} đ</td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-green-600 font-bold">✔ Đã cất vào kho</span>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($inbound->status === 'pending' && count($inbound->items ?? []) > 0)
            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="submit" form="cancel-form" class="bg-white border border-red-300 text-red-700 px-4 py-2 rounded-lg font-medium hover:bg-red-50" onclick="return confirm('Bạn chắc chắn muốn hủy bỏ toàn bộ phiếu nhập này?');">Hủy Phiếu Nháp</button>
                
                <!-- Nút Submit của Complete Form -->
                <button type="submit" form="complete-form" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-green-700 shadow-md" onclick="return confirm('Hàng hóa sẽ được cộng vào tồn kho tại các vị trí đã chọn và KHÔNG THỂ SỬA ĐƯỢC NỮA. Xác nhận chốt phiếu?');">
                    ✔ HOÀN TẤT & NHẬP KHO
                </button>
            </div>
        @endif

        <!-- ================= CÁC FORM NGẦM ĐỂ XỬ LÝ DỮ LIỆU ================= -->
        <!-- Form Hoàn Tất -->
        <form id="complete-form" action="{{ route('inbounds.complete', $inbound->id) }}" method="POST" class="hidden">@csrf</form>
        
        <!-- Form Hủy -->
        <form id="cancel-form" action="{{ route('inbounds.cancel', $inbound->id) }}" method="POST" class="hidden">@csrf</form>
        
        @if($inbound->status === 'pending')
            @foreach($inbound->items ?? [] as $item)
                <!-- Form Update từng dòng -->
                <form id="update-{{$item->id}}" action="{{ route('inbounds.items.update', [$inbound->id, $item->id]) }}" method="POST" class="hidden">@csrf @method('PUT')</form>
                
                <!-- Form Delete từng dòng -->
                <form id="del-{{$item->id}}" action="{{ route('inbounds.removeItem', [$inbound->id, $item->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
            @endforeach
        @endif
        <!-- ================================================================= -->
    </div>
</div>
@endsection