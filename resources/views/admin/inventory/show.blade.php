@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Chi tiết Tồn kho #{{ $inventory->id }}</h2>
        <a href="{{ route('inventory.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
            &larr; Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cột thông tin cơ bản -->
        <div class="lg:col-span-2 bg-white shadow-sm rounded-xl p-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Thông tin sản phẩm -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Thông tin Sản phẩm</h3>
                    <div>
                        <p class="text-sm text-gray-500">Tên sản phẩm</p>
                        <p class="font-bold text-lg text-indigo-700">{{ $inventory->product->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Danh mục & Thương hiệu</p>
                        <p class="font-semibold text-gray-800">
                            {{ $inventory->product->category->name ?? 'N/A' }} / {{ $inventory->product->brand->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Đơn giá bán</p>
                        <p class="font-semibold text-gray-800">{{ number_format($inventory->product->price ?? 0) }} đ</p>
                    </div>
                </div>

                <!-- Thông tin lưu trữ -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Thông tin Lưu trữ</h3>
                    <div>
                        <p class="text-sm text-gray-500">Thuộc Nhà kho</p>
                        <p class="font-bold text-gray-800">{{ $inventory->location->warehouse->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Vị trí thực tế (Location)</p>
                        <p class="font-bold text-indigo-600 text-lg">
                            {{ $inventory->location->name ?? 'N/A' }} 
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded uppercase ml-2">{{ $inventory->location->type ?? '' }}</span>
                        </p>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 mt-2">
                        <p class="text-sm text-indigo-600 font-semibold mb-1">SỐ LƯỢNG ĐANG TỒN</p>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-4xl font-black {{ $inventory->quantity > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $inventory->quantity }}
                            </p>
                            @if($inventory->reserved_quantity > 0)
                                <p class="text-sm font-bold text-amber-600">
                                    (Đang giữ chỗ: {{ $inventory->reserved_quantity }})
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Thống kê Nhập / Xuất (THÊM MỚI) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">Thống kê Giao dịch</h3>
                
                <div class="space-y-5">
                    <div class="flex items-center p-3 bg-slate-50 rounded-lg border border-slate-100">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-md mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-semibold uppercase">Lần nhập gần nhất</p>
                            <p class="text-sm font-bold text-slate-800">
                                {{ $stats['last_import_date'] ? \Carbon\Carbon::parse($stats['last_import_date'])->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')  : 'Chưa có dữ liệu nhập' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                        <div class="p-2 bg-emerald-200 text-emerald-700 rounded-md mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-600 font-semibold uppercase">Tổng đã nhập vào</p>
                            <p class="text-xl font-black text-emerald-700">{{ $stats['total_imported'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-rose-50 rounded-lg border border-rose-100">
                        <div class="p-2 bg-rose-200 text-rose-700 rounded-md mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-rose-600 font-semibold uppercase">Tổng đã xuất ra</p>
                            <p class="text-xl font-black text-rose-700">{{ $stats['total_exported'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Các nút thao tác -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('inventory.edit', $inventory->id) }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-sm">
            Chỉnh sửa tồn kho
        </a>
        <form action="{{ route('inventory.destroy', $inventory->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Xóa dòng tồn kho này sẽ làm sai lệch dữ liệu. Tiếp tục?');">
            @csrf @method('DELETE')
            <button type="submit" class="bg-white text-red-600 border border-red-200 px-6 py-2 rounded-lg font-semibold hover:bg-red-50 transition shadow-sm">
                Xóa dòng này
            </button>
        </form>
    </div>
</div>
@endsection