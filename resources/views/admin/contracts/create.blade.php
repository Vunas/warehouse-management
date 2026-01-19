@extends('layouts.admin')

@section('title', 'Tạo Hợp đồng')
@section('header', 'Đăng ký Hợp đồng Thuê Kho')

@section('content')
<div class="max-w-6xl mx-auto">
    <form action="{{ route('contracts.store') }}" method="POST" id="contractForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Cột Trái: Thông tin chung (4/12) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Card Khách hàng -->
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Thông tin Khách hàng</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng <span class="text-red-500">*</span></label>
                        <select name="customer_id" class="w-full border rounded px-3 py-2 text-sm bg-gray-50 focus:ring-blue-500">
                            <option value="">-- Chọn khách hàng --</option>
                            @foreach($customers as $cus)
                                <option value="{{ $cus->id }}" {{ old('customer_id') == $cus->id ? 'selected' : '' }}>
                                    {{ $cus->company_name }} ({{ $cus->tax_code }})
                                    {{ $cus->user?->full_name }} ({{ $cus->user?->email }})
                                    
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã hợp đồng <span class="text-red-500">*</span></label>
                        <input type="text" name="contract_code" class="w-full border rounded px-3 py-2 text-sm uppercase font-mono" placeholder="VD: CTR-2024-001" value="{{ old('contract_code') }}">
                        @error('contract_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Card Thời hạn -->
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Thời hạn & Điều khoản</h3>
                    
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="w-full border rounded px-2 py-1.5 text-sm" value="{{ old('start_date') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="w-full border rounded px-2 py-1.5 text-sm" value="{{ old('end_date') }}">
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phí phạt (%)</label>
                        <input type="number" name="penalty_markup" value="{{ old('penalty_markup', 0) }}" class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>

                <!-- Card Tổng kết -->
                <div class="bg-blue-50 p-5 rounded-lg border border-blue-100 sticky top-4">
                    <h3 class="font-bold text-blue-800 mb-2">Tổng kết chọn thuê</h3>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-blue-600">Số lượng lô:</span>
                        <span class="font-bold" id="total-blocks">0</span>
                    </div>
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-blue-600">Tổng sức chứa:</span>
                        <span class="font-bold"><span id="total-capacity">0</span> slots</span>
                    </div>
                    <div class="pt-3 border-t border-blue-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-700">Tổng tiền dự kiến:</span>
                            <span class="text-xl font-bold text-red-600" id="total-price">0</span>
                        </div>
                        <div class="text-right text-xs text-gray-500 italic">VNĐ</div>
                    </div>
                    
                    <button type="submit" class="w-full mt-4 bg-blue-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-blue-700 shadow-sm transition">
                        Hoàn tất Hợp đồng
                    </button>
                </div>
            </div>

            <!-- Cột Phải: Chọn Kho & Lô (8/12) -->
            <div class="lg:col-span-8 space-y-6">
                
                {{-- NHÓM 1: KHO NHỎ (Thuê nguyên căn) --}}
                @php
                    $smallWarehouses = $warehouses->where('type.single_contract', true);
                @endphp
                @if($smallWarehouses->isNotEmpty())
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-house-chimney text-green-500 mr-2"></i> Kho Nhỏ (Thuê nguyên kho)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($smallWarehouses as $wh)
                            <div class="border rounded-lg p-4 hover:border-blue-400 transition bg-gray-50">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-gray-800">{{ $wh->name }}</div>
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded border border-green-200">Available</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">
                                    Tổng sức chứa: <b>{{ $wh->total_slots }}</b> slots <br>
                                    Số lô: {{ $wh->blocks->count() }} (Nguyên kho)
                                </div>
                                
                                <div class="flex items-center gap-3 bg-white p-2 rounded border border-gray-200">
                                    <input type="checkbox" class="w-5 h-5 text-blue-600 cursor-pointer warehouse-checkbox" 
                                           data-wh-id="{{ $wh->id }}"
                                           data-capacity="{{ $wh->total_slots }}"
                                           onchange="toggleWarehouse(this)">
                                    
                                    <div class="flex-1">
                                        <input type="number" class="w-full border-none p-0 text-sm focus:ring-0 text-right font-bold text-gray-700 price-input" 
                                               placeholder="Nhập giá trọn gói" 
                                               oninput="updateTotal()" disabled>
                                    </div>
                                    <span class="text-xs text-gray-400">VNĐ</span>
                                </div>

                                <!-- Hidden inputs cho từng block trong kho nhỏ -->
                                <div id="blocks-container-{{ $wh->id }}" class="hidden">
                                    @foreach($wh->blocks as $index => $block)
                                        <input type="hidden" name="" value="{{ $block->id }}" class="block-id-input">
                                        <!-- Giá của từng block sẽ được chia đều hoặc gán 0, ở đây ta gán giá tổng cho block đầu tiên để đơn giản, các block sau giá 0 -->
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- NHÓM 2: KHO TỔNG (Thuê lẻ từng lô) --}}
                @php
                    $bigWarehouses = $warehouses->where('type.single_contract', false);
                @endphp
                @if($bigWarehouses->isNotEmpty())
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-warehouse text-blue-500 mr-2"></i> Kho Tổng (Thuê lẻ từng Lô)
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($bigWarehouses as $wh)
                            <div class="border rounded-lg overflow-hidden">
                                <!-- Header Kho -->
                                <div class="bg-gray-100 px-4 py-2 flex justify-between items-center cursor-pointer hover:bg-gray-200 transition" onclick="toggleAccordion('wh-{{ $wh->id }}')">
                                    <div class="font-bold text-gray-700 text-sm">
                                        <i class="fa-solid fa-angle-down mr-2"></i> {{ $wh->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Còn trống: {{ $wh->blocks->count() }} lô
                                    </div>
                                </div>

                                <!-- Danh sách Lô -->
                                <div id="wh-{{ $wh->id }}" class="hidden p-3 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-white">
                                    @foreach($wh->blocks as $block)
                                        <div class="border rounded p-3 relative hover:shadow-sm transition block-item">
                                            <div class="flex justify-between items-start mb-2">
                                                <span class="font-mono font-bold text-blue-600">{{ $block->block_code }}</span>
                                                <span class="text-xs bg-gray-100 px-1 rounded">{{ $block->total_slots }} slots</span>
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" name="blocks[{{ $block->id }}][id]" value="{{ $block->id }}" 
                                                       class="w-4 h-4 text-blue-600 cursor-pointer block-checkbox"
                                                       data-capacity="{{ $block->total_slots }}"
                                                       onchange="toggleBlockInput(this)">
                                                
                                                <input type="number" name="blocks[{{ $block->id }}][price]" 
                                                       class="w-full border rounded px-2 py-1 text-xs text-right block-price" 
                                                       placeholder="Giá thuê" disabled oninput="updateTotal()">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </form>
</div>

<script>
    function toggleAccordion(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function formatCurrency(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }

    // Logic cho Kho Tổng (Check lẻ)
    function toggleBlockInput(checkbox) {
        const input = checkbox.closest('.block-item').querySelector('.block-price');
        input.disabled = !checkbox.checked;
        if (!checkbox.checked) input.value = '';
        
        // Hightlight box
        const box = checkbox.closest('.block-item');
        if (checkbox.checked) box.classList.add('border-blue-500', 'bg-blue-50');
        else box.classList.remove('border-blue-500', 'bg-blue-50');

        updateTotal();
    }

    // Logic cho Kho Nhỏ (Check nguyên kho)
    function toggleWarehouse(checkbox) {
        const container = checkbox.closest('div.border'); // The warehouse card
        const priceInput = container.querySelector('.price-input');
        const hiddenBlocksDiv = document.getElementById('blocks-container-' + checkbox.dataset.whId);
        
        priceInput.disabled = !checkbox.checked;
        if (!checkbox.checked) priceInput.value = '';

        // Style
        if (checkbox.checked) {
            container.classList.add('border-green-500', 'bg-green-50');
            container.classList.remove('bg-gray-50');
        } else {
            container.classList.remove('border-green-500', 'bg-green-50');
            container.classList.add('bg-gray-50');
        }

        // Tạo/Xóa hidden input name để gửi form
        // Logic: Nếu check, gán name="blocks[ID][...]" cho các hidden input
        const blockInputs = hiddenBlocksDiv.querySelectorAll('.block-id-input');
        
        if (checkbox.checked) {
            // Lấy giá tổng chia đều hoặc gán vào block đầu tiên (ở đây gán block đầu tiên chịu giá, các block sau giá 0)
            // Vì ta submit form, cần logic JS gán name động
            // Đơn giản hóa: Kho nhỏ thường chỉ có 1 block. Nếu có nhiều, ta loop.
            blockInputs.forEach((input, index) => {
                const blockId = input.value;
                input.name = `blocks[${blockId}][id]`;
                
                // Tạo thêm input hidden price
                // Xóa cũ nếu có
                const oldPrice = hiddenBlocksDiv.querySelector(`.price-${blockId}`);
                if (oldPrice) oldPrice.remove();

                const hiddenPrice = document.createElement('input');
                hiddenPrice.type = 'hidden';
                hiddenPrice.className = `price-${blockId} real-price-value`; 
                hiddenPrice.name = `blocks[${blockId}][price]`;
                hiddenPrice.value = (index === 0) ? priceInput.value : 0; // Giá dồn vào block đầu
                hiddenBlocksDiv.appendChild(hiddenPrice);
            });
        } else {
            // Xóa name để không submit
            blockInputs.forEach(input => input.name = "");
            hiddenBlocksDiv.querySelectorAll('input[type="hidden"]:not(.block-id-input)').forEach(el => el.remove());
        }

        updateTotal();
    }

    // Hàm tính tổng chung
    function updateTotal() {
        let totalBlocks = 0;
        let totalCapacity = 0;
        let totalPrice = 0;

        // 1. Tính từ Kho Tổng (Lẻ)
        document.querySelectorAll('.block-checkbox:checked').forEach(cb => {
            totalBlocks++;
            totalCapacity += parseInt(cb.dataset.capacity);
            const price = cb.closest('.block-item').querySelector('.block-price').value;
            totalPrice += price ? parseInt(price) : 0;
        });

        // 2. Tính từ Kho Nhỏ (Nguyên căn)
        document.querySelectorAll('.warehouse-checkbox:checked').forEach(whCb => {
            // Cần đếm số lượng block thật sự trong kho nhỏ này
            const hiddenDiv = document.getElementById('blocks-container-' + whCb.dataset.whId);
            const count = hiddenDiv.querySelectorAll('.block-id-input').length;
            totalBlocks += count;
            totalCapacity += parseInt(whCb.dataset.capacity); // Capacity của cả kho
            
            const container = whCb.closest('div.border');
            const priceInput = container.querySelector('.price-input');
            const priceVal = priceInput.value ? parseInt(priceInput.value) : 0;
            totalPrice += priceVal;

            // Update hidden price inputs value cho realtime submit
            const firstPriceInput = hiddenDiv.querySelector('.real-price-value');
            if(firstPriceInput) firstPriceInput.value = priceVal;
        });

        document.getElementById('total-blocks').innerText = totalBlocks;
        document.getElementById('total-capacity').innerText = number_format(totalCapacity);
        document.getElementById('total-price').innerText = formatCurrency(totalPrice);
    }

    function number_format(number) {
        return new Intl.NumberFormat('en-US').format(number);
    }
</script>
@endsection