@extends('layouts.admin')

@php
    $isEdit = isset($inventory);
    $action = $isEdit ? route('inventory.update', $inventory->id) : route('inventory.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Điều chỉnh Số lượng Tồn kho' : 'Thêm Tồn kho mới';
@endphp

@section('content')
    <!-- Tích hợp TomSelect CSS (Thư viện tạo Dropdown Search) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control { border-radius: 0.5rem; padding: 0.65rem 0.75rem; border-color: #cbd5e1; min-height: 44px; font-weight: 600; color: #1e293b; background-color: #fff;}
        .ts-control.focus { box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2); border-color: #4f46e5; }
        .ts-wrapper.disabled .ts-control { background-color: #f1f5f9; cursor: not-allowed;}
        .ts-dropdown { border-radius: 0.5rem; border-color: #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); font-weight: 500;}
    </style>

    <x-crud.form 
        :title="$title" 
        :action="$action" 
        :method="$method" 
        cancelRoute="{{ route('inventory.index') }}"
    >
        <div class="space-y-6 md:col-span-2 lg:col-span-1 max-w-2xl">
            
            @if($isEdit)
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg shadow-sm mb-4">
                    <p class="text-sm text-amber-800 font-semibold">
                        ⚠️ <b>Chế độ sửa:</b> Bạn chỉ được phép điều chỉnh TỔNG SỐ LƯỢNG THỰC TẾ tại vị trí này. Nếu bạn muốn đổi sản phẩm sang Kệ khác hoặc Kho khác, vui lòng dùng tính năng <a href="{{ route('transfers.index') }}" class="underline text-indigo-700 font-black hover:text-indigo-900">Luân chuyển kho</a>.
                    </p>
                </div>
            @endif

            <!-- Chọn Sản Phẩm -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Sản phẩm <span class="text-rose-500">*</span></label>
                @if($isEdit)
                    <input type="hidden" name="product_id" value="{{ $inventory->product_id }}">
                    <input type="text" value="{{ $inventory->product->name }} (Mã: SP-{{ $inventory->product_id }})" disabled class="block w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 font-bold shadow-sm">
                @else
                    <select name="product_id" id="product_id" required class="block w-full" placeholder="-- Gõ tên hoặc mã sản phẩm để tìm --">
                        <option value="">-- Gõ tên hoặc mã sản phẩm để tìm --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Mã: SP-{{ $product->id }})
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Chọn Lô Hàng -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Lô sản phẩm (Batch) <span class="text-rose-500">*</span></label>
                @if($isEdit)
                    <input type="hidden" name="batch_id" value="{{ $inventory->batch_id }}">
                    @php
                        $b = $inventory->batch;
                        $batchText = $b ? "Lô: {$b->batch_code} | HSD: " . ($b->expiry_date ? \Carbon\Carbon::parse($b->expiry_date)->format('d/m/Y') : 'Không có') : 'Không chia lô';
                    @endphp
                    <input type="text" value="{{ $batchText }}" disabled class="block w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 font-bold shadow-sm">
                @else
                    <!-- Native select ban đầu bị disable, khi user chọn Sản phẩm thì JS sẽ lọc lô và kích hoạt -->
                    <select name="batch_id" id="batch_id" required class="block w-full disabled:opacity-50" disabled placeholder="-- Chọn Sản phẩm trước --">
                        <option value="">-- Vui lòng chọn Sản phẩm trước --</option>
                    </select>
                @endif
            </div>

            <!-- Khu vực chọn Kho và Vị trí (Chỉ hiện khi Thêm mới) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(!$isEdit)
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nhà Kho (Warehouse) <span class="text-rose-500">*</span></label>
                    <select id="warehouse_id" class="block w-full" placeholder="-- Chọn nhà kho --">
                        <option value="">-- Chọn nhà kho --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="{{ $isEdit ? 'md:col-span-2' : '' }}">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Vị trí Kệ (Location) <span class="text-rose-500">*</span></label>
                    @if($isEdit)
                        <input type="hidden" name="location_id" value="{{ $inventory->location_id }}">
                        <input type="text" value="{{ $inventory->location->warehouse->name ?? '' }} ➔ Kệ: {{ $inventory->location->name ?? '' }}" disabled class="block w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 font-bold shadow-sm">
                    @else
                        <!-- Native select ban đầu bị disable, khi user chọn Warehouse thì JS sẽ fetch API và kích hoạt TomSelect -->
                        <select name="location_id" id="location_id" required class="block w-full disabled:opacity-50" disabled placeholder="-- Chọn Nhà kho trước --">
                            <option value="">-- Vui lòng chọn Nhà kho trước --</option>
                        </select>
                    @endif
                </div>
            </div>

            <!-- Khu vực Nhập Số Lượng -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-200 mt-6">
                <div class="md:col-span-1">
                    <label class="block text-sm font-bold text-slate-800 mb-2">Số lượng Tổng (Thực tế) <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" value="{{ old('quantity', $inventory->quantity ?? 0) }}" required min="0" class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-2xl font-black text-indigo-700 shadow-sm text-center">
                </div>
                
                @if($isEdit)
                <div class="md:col-span-1">
                    <label class="block text-sm font-bold text-slate-500 mb-2">SL đang Giữ chỗ (Reserved)</label>
                    <input type="text" value="{{ $inventory->reserved_quantity ?? 0 }}" disabled class="block w-full px-4 py-3 border border-amber-200 rounded-lg bg-amber-50 text-amber-700 text-2xl font-black shadow-inner cursor-not-allowed text-center" title="Số lượng này đang chờ xuất.">
                </div>
                
                <div class="md:col-span-2 pt-4 border-t border-slate-200 flex justify-between items-center">
                    <span class="text-sm text-slate-600 font-bold uppercase tracking-wider">Khả dụng xuất bán:</span>
                    <span class="text-3xl font-black text-emerald-600">{{ ($inventory->quantity ?? 0) - ($inventory->reserved_quantity ?? 0) }}</span>
                </div>
                @endif
            </div>
            
        </div>

        @if(!$isEdit)
        <!-- Khởi tạo TomSelect cho trang Thêm Mới -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Config chung cho tất cả input
                const tsConfig = {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    placeholder: function() { return this.input.getAttribute('placeholder'); }
                };

                // Kích hoạt ngay cho Product và Warehouse
                const productTs = new TomSelect("#product_id", tsConfig);
                const warehouseSelect = new TomSelect("#warehouse_id", tsConfig);
                
                // ==========================================
                // 1. XỬ LÝ LÔ HÀNG DỰA THEO SẢN PHẨM
                // ==========================================
                const allBatches = @json($batches); // Lấy toàn bộ dữ liệu batch từ PHP
                const batchNativeSelect = document.getElementById('batch_id');
                let batchTomSelect = null;
                const oldBatchId = "{{ old('batch_id') }}"; // Lưu lại giá trị nếu submit lỗi
                
                function updateBatches(productId) {
                    if (batchTomSelect) {
                        batchTomSelect.destroy();
                        batchTomSelect = null;
                    }
                    
                    batchNativeSelect.innerHTML = '';
                    batchNativeSelect.disabled = true;

                    if (!productId) {
                        batchNativeSelect.innerHTML = '<option value="">-- Vui lòng chọn Sản phẩm trước --</option>';
                        return;
                    }

                    // Lọc những lô hàng thuộc về product_id đã chọn
                    const filteredBatches = allBatches.filter(b => b.product_id == productId);

                    if (filteredBatches.length === 0) {
                        batchNativeSelect.innerHTML = '<option value="">-- Sản phẩm này chưa được tạo Lô hàng nào --</option>';
                    } else {
                        batchNativeSelect.innerHTML = '<option value="">-- Chọn hoặc gõ tìm mã lô --</option>';
                        filteredBatches.forEach(batch => {
                            const option = document.createElement('option');
                            option.value = batch.id;
                            
                            // Định dạng ngày hiển thị
                            let dateStr = 'Không có';
                            if (batch.expiry_date) {
                                const d = new Date(batch.expiry_date);
                                const day = String(d.getDate()).padStart(2, '0');
                                const month = String(d.getMonth() + 1).padStart(2, '0');
                                dateStr = `${day}/${month}/${d.getFullYear()}`;
                            }

                            option.textContent = `Lô: ${batch.batch_code} | HSD: ${dateStr}`;
                            
                            // Tự động select lại nếu có old()
                            if (batch.id == oldBatchId) option.selected = true;
                            
                            batchNativeSelect.appendChild(option);
                        });
                        batchNativeSelect.disabled = false;
                        batchTomSelect = new TomSelect("#batch_id", tsConfig);
                    }
                }

                // Lắng nghe sự kiện đổi Sản phẩm
                productTs.on('change', updateBatches);
                // Chạy 1 lần lúc load trang (nếu có old value)
                if (productTs.getValue()) {
                    updateBatches(productTs.getValue());
                }

                // ==========================================
                // 2. XỬ LÝ VỊ TRÍ DỰA THEO NHÀ KHO
                // ==========================================
                const locationNativeSelect = document.getElementById('location_id');
                let locationTomSelect = null;
                const oldLocationId = "{{ old('location_id') }}"; // Lưu lại giá trị location nếu submit lỗi

                async function updateLocations(wId) {
                    if (locationTomSelect) {
                        locationTomSelect.destroy();
                        locationTomSelect = null;
                    }
                    
                    locationNativeSelect.innerHTML = '<option value="">-- Đang tải dữ liệu... --</option>';
                    locationNativeSelect.disabled = true;

                    if (!wId) {
                        locationNativeSelect.innerHTML = '<option value="">-- Vui lòng chọn Nhà kho trước --</option>';
                        return;
                    }

                    try {
                        const response = await fetch(`/api/locations/${wId}`);
                        const locations = await response.json();

                        locationNativeSelect.innerHTML = '<option value="">-- Gõ tìm kiếm Kệ chứa (VD: A01) --</option>';
                        
                        if(locations.length === 0) {
                            locationNativeSelect.innerHTML = '<option value="">-- Kho này chưa có Kệ nào --</option>';
                        } else {
                            locations.forEach(loc => {
                                const option = document.createElement('option');
                                option.value = loc.id;
                                const displayName = loc.full_path ? loc.full_path : loc.name;
                                option.textContent = `${displayName} (Loại: ${loc.type.toUpperCase()})`;
                                
                                // Tự động select lại nếu có old()
                                if (loc.id == oldLocationId) option.selected = true;

                                locationNativeSelect.appendChild(option);
                            });
                            locationNativeSelect.disabled = false;
                            
                            locationTomSelect = new TomSelect("#location_id", tsConfig);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        locationNativeSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
                    }
                }

                // Lắng nghe sự kiện đổi Nhà kho
                warehouseSelect.on('change', updateLocations);
                // Chạy 1 lần lúc load trang (nếu có old value)
                if (warehouseSelect.getValue()) {
                    updateLocations(warehouseSelect.getValue());
                }
            });
        </script>
        @endif
    </x-crud.form>
@endsection