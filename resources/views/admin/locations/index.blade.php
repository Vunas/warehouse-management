@extends('layouts.admin')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- CỘT TRÁI: HIỂN THỊ CÂY THƯ MỤC KHO -->
    <div class="lg:col-span-2 bg-white shadow-sm rounded-xl p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold text-gray-800">Sơ Đồ Vị Trí Kho</h2>
            
            <!-- Chọn Kho -->
            <form action="{{ route('locations.index') }}" method="GET" class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-600">Xem kho:</label>
                <select name="warehouse_id" onchange="this.form.submit()" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm font-bold text-indigo-700 focus:ring-indigo-500">
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ $warehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 min-h-[400px]">
            @if(count($locationsTree) > 0)
                <div class="text-sm">
                    @include('admin.locations.partials.tree_node', ['nodes' => $locationsTree])
                </div>
            @else
                <div class="text-center text-gray-500 py-12">
                    <p>Kho này chưa có sơ đồ vị trí nào.</p>
                    <p class="text-sm mt-2">Hãy tạo Zone đầu tiên ở bảng bên cạnh.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- CỘT PHẢI: FORM TẠO VỊ TRÍ MỚI -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-200 sticky top-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Thêm Vị trí mới</h3>
            <form action="{{ route('locations.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouseId }}">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Vị trí cấp Cha (Parent)</label>
                    <select name="parent_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                        <option value="">[Trống] -- Trực thuộc Kho tổng</option>
                        @foreach($flatLocations as $loc)
                            <option value="{{ $loc->id }}">{{ str_repeat('-- ', $loc->level ?? 0) }}{{ $loc->name }} ({{ $loc->type }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Để trống nếu đây là Khu vực chính (Zone)</p>
                </div>

                <x-ui.input name="name" label="Tên vị trí (VD: Dãy A, Kệ 1...)" required="true" />

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Loại vị trí</label>
                    <select name="type" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                        <option value="zone">Zone (Khu vực)</option>
                        <option value="rack">Rack (Dãy kệ lớn)</option>
                        <option value="shelf">Shelf (Ngăn kệ)</option>
                        <option value="pallet">Pallet</option>
                        <option value="bin">Bin (Thùng/Khay nhỏ)</option>
                    </select>
                </div>

                <div class="flex items-start bg-indigo-50 p-3 rounded-lg border border-indigo-100 mt-2">
                    <div class="flex items-center h-5">
                        <input id="is_store" name="is_store" type="checkbox" value="1" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_store" class="font-bold text-gray-800 cursor-pointer">Cho phép lưu trữ hàng</label>
                        <p class="text-gray-600 mt-1 text-xs">Chỉ đánh dấu vào đây nếu vị trí này là điểm cuối cùng thực sự chứa hàng (Ví dụ: Thùng Bin, Ngăn Shelf).</p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-indigo-700 transition mt-4">
                    Thêm Vị Trí
                </button>
            </form>
        </div>
    </div>
</div>
@endsection