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
    <!-- Tùy chỉnh CSS để TomSelect hợp với Tailwind -->
    <style>
        .ts-control { border-radius: 0.5rem; padding: 0.5rem 0.75rem; border-color: #d1d5db; min-height: 42px; }
        .ts-control.focus { box-shadow: 0 0 0 1px #6366f1; border-color: #6366f1; }
    </style>

    <x-crud.form 
        :title="$title" 
        :action="$action" 
        :method="$method" 
        cancelRoute="{{ route('inventory.index') }}"
    >
        <div class="space-y-6 md:col-span-2 lg:col-span-1 max-w-xl">
            
            @if($isEdit)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <p class="text-sm text-yellow-700">Chế độ sửa: Chỉ cho phép điều chỉnh số lượng. Nếu bạn muốn đổi vị trí hàng hóa, vui lòng sử dụng chức năng <b>Chuyển kho</b>.</p>
                </div>
            @endif

            <!-- Chọn Sản Phẩm -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sản phẩm *</label>
                @if($isEdit)
                    <input type="hidden" name="product_id" value="{{ $inventory->product_id }}">
                    <input type="text" value="{{ $inventory->product->name }}" disabled class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 sm:text-sm">
                @else
                    <select name="product_id" id="product_id" required class="block w-full">
                        <option value="">-- Chọn hoặc gõ tìm kiếm sản phẩm --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (SKU: {{ $product->sku ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Lô sản phẩm (Batch) *
                </label>
                <select name="batch_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- Chọn lô hàng --</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}">
                            {{ $batch->batch_code }} 
                            (HSD: {{ $batch->expiry_date ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>


            <!-- Chọn Nhà Kho (Chỉ hiện khi Thêm mới) -->
            @if(!$isEdit)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nhà Kho (Warehouse) *</label>
                <select id="warehouse_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">-- Chọn Nhà kho trước --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Chọn Vị Trí (Location) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Vị trí lưu trữ (is_store = true) *</label>
                @if($isEdit)
                    <input type="hidden" name="location_id" value="{{ $inventory->location_id }}">
                    <input type="text" value="{{ $inventory->location->warehouse->name ?? '' }} - {{ $inventory->location->full_path ?? $inventory->location->name }}" disabled class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 sm:text-sm">
                @else
                    <select name="location_id" id="location_id" required class="block w-full disabled:bg-gray-100" disabled>
                        <option value="">-- Vui lòng chọn Nhà kho trước --</option>
                    </select>
                @endif
            </div>

            <x-ui.input 
                name="quantity" 
                type="number" 
                label="Số lượng thực tế *" 
                :value="old('quantity', $inventory->quantity ?? 0)" 
                required="true" 
            />
        </div>

        @if(!$isEdit)
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Khởi tạo tìm kiếm cho Sản phẩm
                new TomSelect("#product_id", {
                    create: false,
                    sortField: { field: "text", direction: "asc" }
                });

                const warehouseSelect = document.getElementById('warehouse_id');
                const locationSelect = document.getElementById('location_id');
                let locationTomSelect = null;

                warehouseSelect.addEventListener('change', async function() {
                    const wId = this.value;
                    
                    // Xóa instance TomSelect cũ nếu có
                    if (locationTomSelect) {
                        locationTomSelect.destroy();
                        locationTomSelect = null;
                    }
                    
                    locationSelect.innerHTML = '<option value="">-- Đang tải dữ liệu... --</option>';
                    locationSelect.disabled = true;

                    if (!wId) {
                        locationSelect.innerHTML = '<option value="">-- Vui lòng chọn Nhà kho trước --</option>';
                        return;
                    }

                    try {
                        const response = await fetch(`/api/locations/${wId}`);
                        const locations = await response.json();

                        locationSelect.innerHTML = '<option value="">-- Gõ để tìm kiếm vị trí (VD: Z2-A01) --</option>';
                        
                        if(locations.length === 0) {
                            locationSelect.innerHTML = '<option value="">-- Kho này không có vị trí nào lưu được hàng --</option>';
                        } else {
                            locations.forEach(loc => {
                                const option = document.createElement('option');
                                option.value = loc.id;
                                // Sử dụng full_path nếu có, không thì dùng name
                                const displayName = loc.full_path ? loc.full_path : loc.name;
                                option.textContent = `${displayName} (Loại: ${loc.type.toUpperCase()})`;
                                locationSelect.appendChild(option);
                            });
                            locationSelect.disabled = false;
                            
                            // Khởi tạo TomSelect cho Location sau khi đã render option
                            locationTomSelect = new TomSelect("#location_id", {
                                create: false,
                                sortField: { field: "text", direction: "asc" }
                            });
                        }
                    } catch (error) {
                        console.error('Error fetching locations:', error);
                        locationSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
                    }
                });
            });
        </script>
        @endif
    </x-crud.form>
@endsection