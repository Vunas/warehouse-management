@extends('layouts.admin')

@section('title', 'Sơ đồ kho: ' . $warehouse->name)
@section('header')
    <div class="flex items-center gap-2">
        <a href="{{ route('warehouses.index') }}" class="text-gray-400 hover:text-blue-600"><i class="fa-solid fa-arrow-left"></i></a>
        <span>{{ $warehouse->name }}</span>
        <span class="text-xs px-2 py-1 rounded border {{ $warehouse->status == 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
            {{ ucfirst($warehouse->status) }}
        </span>
        <span class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-200">
            {{ $warehouse->type->description ?? $warehouse->type->type_code }}
        </span>
    </div>
@endsection

@section('content')
<div class="flex h-[calc(100vh-140px)] gap-6 overflow-hidden">
    
    <!-- KHUNG TRÁI: SƠ ĐỒ TRỰC QUAN -->
    <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <!-- Toolbar & Legend -->
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <!-- Thống kê nhanh -->
            <div class="flex items-center gap-6">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-bold">Tổng sức chứa</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($stats['total_capacity']) }} <span class="text-xs font-normal text-gray-500">slots</span></p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-bold">Đã sử dụng</p>
                    <div class="flex items-center gap-2">
                        <p class="text-lg font-bold {{ $stats['usage_percent'] > 90 ? 'text-red-600' : 'text-blue-600' }}">{{ $stats['used_slots'] }}</p>
                        <span class="text-xs bg-gray-200 px-1.5 rounded">{{ $stats['usage_percent'] }}%</span>
                    </div>
                </div>
            </div>

            <!-- Chú thích màu sắc -->
            <div class="flex items-center gap-3 text-xs">
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-gray-100 border border-gray-300"></span> Trống</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 border border-green-300"></span> Còn chỗ</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-100 border border-orange-300"></span> Đầy (>90%)</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-100 border border-blue-300"></span> Đã thuê (HĐ)</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 border border-red-300"></span> Khóa/Bảo trì</div>
            </div>
        </div>

        <!-- Grid Container -->
        <div class="flex-1 overflow-y-auto p-6 bg-slate-100 custom-scrollbar">
            
            <!-- LOGIC HIỂN THỊ: Tùy loại kho -->
            @if(isset($warehouse->type) && $warehouse->type->type_code == 'SMALL')
                {{-- GIAO DIỆN KHO NHỎ (1 BLOCK LỚN) --}}
                <div class="h-full flex flex-col justify-center items-center">
                    @php 
                        $block = $warehouse->blocks->first(); 
                        // Tìm contract đang active (ngày kết thúc >= hôm nay)
                        $contractBlock = $block ? $block->contractBlocks->where('rented_to', '>=', now())->first() : null;
                        $customer = $contractBlock ? $contractBlock->contract->customer : null;
                        
                        $used = $block ? $block->inventoryItems->sum('slot_used') : 0;
                        $percent = ($block && $block->total_slots > 0) ? ($used / $block->total_slots) * 100 : 0;
                        
                        // Chuẩn bị dữ liệu an toàn cho JS
                        $blockJson = $block ? json_encode($block) : '{}';
                        $customerName = $customer ? $customer->company_name : '';
                    @endphp

                    @if($block)
                    <div onclick="showBlockDetail({{ $blockJson }}, {{ $used }}, '{{ $customerName }}')" 
                         class="w-full max-w-2xl bg-white border-2 {{ $customer ? 'border-blue-400 ring-4 ring-blue-50' : 'border-gray-300 border-dashed' }} rounded-xl p-8 cursor-pointer hover:shadow-lg transition-all transform hover:-translate-y-1 relative group">
                        
                        <h3 class="text-2xl font-bold text-gray-700 mb-2 flex items-center justify-center">
                            <i class="fa-solid fa-warehouse mr-3 text-gray-400"></i> {{ $warehouse->name }}
                        </h3>
                        
                        @if($customer)
                            <div class="text-center mb-6">
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold border border-blue-200">
                                    <i class="fa-solid fa-user-check mr-1"></i> Đang thuê: {{ $customer->company_name }}
                                </span>
                                <p class="text-xs text-gray-500 mt-2">HĐ: {{ $contractBlock->contract->contract_code }} (Đến {{ $contractBlock->rented_to->format('d/m/Y') }})</p>
                            </div>
                        @else
                            <div class="text-center mb-6">
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-sm font-bold">
                                    Chưa có hợp đồng thuê
                                </span>
                            </div>
                        @endif

                        <!-- Progress Bar Lớn -->
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div><span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">Sức chứa</span></div>
                                <div class="text-right"><span class="text-xs font-semibold inline-block text-blue-600">{{ number_format($percent, 1) }}% ({{ $used }}/{{ $block->total_slots }})</span></div>
                            </div>
                            <div class="overflow-hidden h-6 mb-4 text-xs flex rounded bg-blue-100 border border-blue-200">
                                <div style="width:{{ $percent }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
                            </div>
                        </div>
                        
                        <p class="text-center text-xs text-gray-400 mt-4 group-hover:text-blue-500 transition">Nhấn để xem chi tiết tồn kho</p>
                    </div>
                    @else
                        <div class="text-center text-gray-400">Chưa cấu hình Lô (Block) cho kho này.</div>
                    @endif
                </div>

            @else
                {{-- GIAO DIỆN KHO TỔNG / TRUNG CHUYỂN (GRID BLOCKS) --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($warehouse->blocks as $block)
                        @php
                            $used = $block->inventoryItems->sum('slot_used');
                            $total = $block->total_slots;
                            $percent = $total > 0 ? ($used / $total) * 100 : 0;
                            
                            $bgClass = 'bg-white';
                            $borderClass = 'border-gray-200';
                            $barClass = 'bg-green-500';
                            $statusIcon = '<i class="fa-solid fa-box-open text-gray-300"></i>';

                            $isRented = $block->contractBlocks->where('rented_to', '>=', now())->first();
                            $customerName = $isRented ? $isRented->contract->customer->company_name : '';
                            
                            if ($block->status == 'locked') {
                                $bgClass = 'bg-red-50'; $borderClass = 'border-red-200'; $statusIcon = '<i class="fa-solid fa-lock text-red-400"></i>';
                            } elseif ($isRented) {
                                $bgClass = 'bg-blue-50'; $borderClass = 'border-blue-300'; $barClass = 'bg-blue-600';
                                $statusIcon = '<i class="fa-solid fa-file-contract text-blue-400" title="Đã thuê"></i>';
                            } elseif ($percent >= 90) {
                                $bgClass = 'bg-orange-50'; $borderClass = 'border-orange-300'; $barClass = 'bg-orange-500';
                                $statusIcon = '<i class="fa-solid fa-triangle-exclamation text-orange-400"></i>';
                            } elseif ($percent > 0) {
                                $statusIcon = '<i class="fa-solid fa-boxes-stacked text-green-500"></i>';
                            }
                        @endphp

                        <div onclick="showBlockDetail({{ json_encode($block) }}, {{ $used }}, '{{ $customerName }}')" 
                             class="relative p-4 rounded-lg border {{ $borderClass }} {{ $bgClass }} hover:shadow-md cursor-pointer transition group">
                            
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-mono font-bold text-gray-700 group-hover:text-blue-600">{{ $block->block_code }}</span>
                                <span class="text-sm">{!! $statusIcon !!}</span>
                            </div>

                            <!-- Progress Mini -->
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2 dark:bg-gray-700">
                                <div class="{{ $barClass }} h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>

                            <div class="flex justify-between items-end">
                                <span class="text-[10px] text-gray-500">{{ $used }} / {{ $total }} slots</span>
                                <span class="text-xs font-bold {{ $percent > 90 ? 'text-red-600' : 'text-gray-600' }}">{{ round($percent) }}%</span>
                            </div>

                            @if($isRented)
                                <div class="mt-2 pt-2 border-t border-gray-200/50 text-[10px] text-blue-600 truncate">
                                    {{ $customerName }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- KHUNG PHẢI: CHI TIẾT (SIDE PANEL) -->
    <div id="detailPanel" class="w-96 bg-white rounded-xl shadow-lg border border-gray-200 flex flex-col transform translate-x-full transition-transform duration-300 fixed right-0 top-0 h-full z-50 md:relative md:transform-none md:h-auto md:z-0 hidden md:flex">
        <!-- Header Panel -->
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <div>
                <h3 class="font-bold text-gray-800" id="panelBlockCode">Chọn Lô/Kệ</h3>
                <p class="text-xs text-gray-500" id="panelWarehouseName">{{ $warehouse->name }}</p>
            </div>
            <button onclick="hidePanel()" class="md:hidden text-gray-400 hover:text-red-500"><i class="fa-solid fa-times"></i></button>
        </div>

        <!-- Content Panel -->
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar" id="panelContent">
            <!-- Trạng thái trống (Default) -->
            <div id="emptyState" class="text-center py-10">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300">
                    <i class="fa-solid fa-cube text-2xl"></i>
                </div>
                <p class="text-sm text-gray-500">Chọn một lô từ sơ đồ để xem chi tiết hàng hóa.</p>
            </div>

            <!-- Chi tiết (Hidden by default) -->
            <div id="detailState" class="hidden">
                <!-- Info Box -->
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-blue-600 font-bold">Trạng thái:</span>
                        <span id="panelStatus" class="font-bold">---</span>
                    </div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Sức chứa:</span>
                        <span id="panelCapacity">---</span>
                    </div>
                    <div class="flex justify-between text-xs" id="panelCustomerRow">
                        <span class="text-gray-500">Khách thuê:</span>
                        <span id="panelCustomer" class="truncate max-w-[150px]">---</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <a href="#" id="btnTransfer" class="text-center bg-white border border-gray-300 text-gray-700 py-1.5 rounded text-xs hover:bg-gray-50">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i> Chuyển đi
                    </a>
                    <a href="#" id="btnInbound" class="text-center bg-blue-600 text-white py-1.5 rounded text-xs hover:bg-blue-700 shadow-sm">
                        <i class="fa-solid fa-plus"></i> Nhập hàng
                    </a>
                </div>

                <!-- Inventory List -->
                <h4 class="font-bold text-xs text-gray-700 uppercase border-b pb-2 mb-3">Danh sách hàng hóa</h4>
                <div id="inventoryList" class="space-y-3">
                    <!-- Items will be injected here via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Overlay cho mobile --}}
<div id="overlay" onclick="hidePanel()" class="fixed inset-0 bg-black/20 z-40 hidden md:hidden"></div>

{{-- FIX: Tách logic PHP ra khỏi JS để tránh ParseError --}}
@php
    $inventoryData = $warehouse->blocks->mapWithKeys(function($block) {
        return [$block->id => $block->inventoryItems->map(function($item) {
            return [
                'product_name' => $item->product->name ?? 'Unknown Product',
                'sku' => $item->product->sku ?? 'N/A',
                'qty' => $item->current_quantity,
                'slot_used' => $item->slot_used,
                'imported' => $item->imported_at ? $item->imported_at->format('d/m/Y') : 'N/A'
            ];
        })];
    });
@endphp

<script>
    // Nhận dữ liệu từ biến PHP đã xử lý
    const inventoryData = @json($inventoryData);

    function showBlockDetail(block, usedSlots, customerName) {
        // 1. UI Toggle
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('detailState').classList.remove('hidden');
        
        // Mobile handling
        const panel = document.getElementById('detailPanel');
        panel.classList.remove('hidden', 'translate-x-full');
        document.getElementById('overlay').classList.remove('hidden');

        // 2. Fill Data
        document.getElementById('panelBlockCode').innerText = 'Lô: ' + block.block_code;
        document.getElementById('panelCapacity').innerText = usedSlots + ' / ' + block.total_slots + ' slots';
        
        const statusEl = document.getElementById('panelStatus');
        if (block.status === 'locked') {
            statusEl.innerText = 'Đang khóa';
            statusEl.className = 'font-bold text-red-600';
        } else if (customerName) {
            statusEl.innerText = 'Đã cho thuê';
            statusEl.className = 'font-bold text-blue-600';
        } else {
            statusEl.innerText = 'Đang hoạt động';
            statusEl.className = 'font-bold text-green-600';
        }

        const customerRow = document.getElementById('panelCustomerRow');
        if (customerName) {
            customerRow.classList.remove('hidden');
            document.getElementById('panelCustomer').innerText = customerName;
        } else {
            customerRow.classList.add('hidden');
        }

        // 3. Render Inventory List
        const listContainer = document.getElementById('inventoryList');
        listContainer.innerHTML = ''; // Clear old

        const items = inventoryData[block.id] || [];

        if (items.length === 0) {
            listContainer.innerHTML = '<div class="text-center text-xs text-gray-400 italic py-4">Lô này đang trống</div>';
        } else {
            items.forEach(item => {
                const itemHtml = `
                    <div class="bg-white border border-gray-100 rounded p-2 shadow-sm hover:border-blue-200 transition">
                        <div class="flex justify-between mb-1">
                            <span class="font-bold text-xs text-gray-800">${item.product_name}</span>
                            <span class="text-[10px] font-mono text-gray-500">${item.sku}</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <div>
                                <div class="text-[10px] text-gray-500">SL: <b class="text-gray-800">${item.qty}</b></div>
                                <div class="text-[10px] text-gray-400">Ngày nhập: ${item.imported}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] bg-blue-50 text-blue-600 px-1.5 rounded font-bold">${item.slot_used} slots</div>
                            </div>
                        </div>
                    </div>
                `;
                listContainer.innerHTML += itemHtml;
            });
        }
    }

    function hidePanel() {
        const panel = document.getElementById('detailPanel');
        if (window.innerWidth < 768) {
            panel.classList.add('translate-x-full');
            setTimeout(() => { panel.classList.add('hidden'); }, 300);
            document.getElementById('overlay').classList.add('hidden');
        } else {
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('detailState').classList.add('hidden');
        }
    }
</script>
@endsection