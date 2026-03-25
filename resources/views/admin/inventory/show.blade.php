@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Chi tiết Tồn kho #{{ $inventory->id }}</h2>
        <a href="{{ route('inventory.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
            &larr; Quay lại danh sách
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-200">
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
                    <p class="text-3xl font-black {{ $inventory->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $inventory->quantity }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('inventory.edit', $inventory->id) }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                Chỉnh sửa tồn kho
            </a>
            <form action="{{ route('inventory.destroy', $inventory->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Xóa dòng tồn kho này sẽ làm sai lệch dữ liệu. Tiếp tục?');">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-6 py-2 rounded-lg font-semibold hover:bg-red-100 transition">
                    Xóa dòng này
                </button>
            </form>
        </div>
    </div>
</div>
@endsection