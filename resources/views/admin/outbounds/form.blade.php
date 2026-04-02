@extends('layouts.admin')

@php
    $isEdit = isset($outbound);
    $action = $isEdit ? route('outbounds.update', $outbound->id) : route('outbounds.store');
    $initialItems = old('items', $isEdit ? $outbound->items->toArray() : []);
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
            <h2 class="text-2xl font-extrabold text-gray-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                {{ $isEdit ? 'Chỉnh sửa Phiếu Xuất Kho' : 'Tạo Phiếu Xuất (Auto-Allocate)' }}
            </h2>
            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-md text-sm font-semibold border border-yellow-200 shadow-sm">Trạng thái: Nháp (Pending)</span>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                <p class="font-bold text-red-700">Vui lòng kiểm tra lại thông tin:</p>
                <ul class="list-disc list-inside text-red-600 text-sm mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded shadow-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ $action }}" method="POST" id="outboundForm">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Chọn Kho XUẤT -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nhà Kho Xuất Hàng <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" id="warehouse_id" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-colors sm:text-sm font-semibold cursor-pointer text-indigo-900">
                        <option value="">-- Vui lòng chọn Nhà Kho trước tiên --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ (old('warehouse_id', $outbound->warehouse_id ?? '')) == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mục đích xuất -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Mục đích xuất kho <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-colors sm:text-sm font-medium cursor-pointer">
                        <option value="sales" {{ (old('type', $outbound->type ?? '')) == 'sales' ? 'selected' : '' }}>🛒 Xuất Bán Hàng (Sales Order)</option>
                        <option value="internal" {{ (old('type', $outbound->type ?? '')) == 'internal' ? 'selected' : '' }}>🏢 Xuất Nội bộ / Sử dụng</option>
                        <option value="adjustment" {{ (old('type', $outbound->type ?? '')) == 'adjustment' ? 'selected' : '' }}>⚙️ Xuất Điều chỉnh / Khác</option>
                    </select>
                </div>

                <!-- Khối hiển thị khi chọn Sales -->
                <div id="div_order" class="md:col-span-2 hidden bg-blue-50 p-5 rounded-xl border border-blue-100 shadow-inner">
                    <label class="block text-sm font-bold text-blue-800 mb-2">Tham chiếu Đơn hàng <span class="text-red-500">*</span></label>
                    <select name="order_id" id="order_id" class="block w-full px-4 py-2.5 border border-blue-200 rounded-lg focus:ring-blue-500 sm:text-sm bg-white font-medium cursor-pointer">
                        <option value="">-- Chọn Đơn hàng để tự động tải sản phẩm --</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ (old('order_id', $outbound->order_id ?? '')) == $order->id ? 'selected' : '' }}>
                                ĐH #{{ $order->id }} - Trạng thái: {{ strtoupper($order->status) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-blue-600 mt-2 font-medium"><i class="fas fa-info-circle"></i> Khi chọn đơn hàng, hệ thống sẽ tự điền sản phẩm và tự động tìm kiếm kệ lấy hàng hợp lý nhất (FEFO).</p>
                </div>

                <!-- Khối hiển thị khi chọn Khác -->
                <div id="div_reason" class="md:col-span-2 hidden bg-gray-50 p-5 rounded-xl border border-gray-200 shadow-inner">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Lý do xuất kho cụ thể <span class="text-red-500">*</span></label>
                    <input type="text" name="reason" value="{{ old('reason', $outbound->reason ?? '') }}" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-indigo-500 sm:text-sm bg-white" placeholder="Ví dụ: Xuất hàng mẫu cho Marketing...">
                </div>
            </div>

            <!-- BẢNG CHỌN SẢN PHẨM -->
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm bg-white">
                <div class="bg-gray-100 px-6 py-4 flex justify-between items-center border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Danh sách Cần xuất</h3>
                        <p class="text-xs text-gray-500 mt-1">Hệ thống sẽ tự động phân bổ kệ lấy hàng dựa trên thuật toán FEFO (Hết hạn trước lấy trước).</p>
                    </div>
                    <button type="button" id="btn_add_block" class="px-5 py-2 bg-white border border-gray-300 text-indigo-700 font-bold rounded-lg shadow-sm hover:bg-indigo-50 transition active:bg-indigo-100 disabled:opacity-50">
                        + Thêm Sản Phẩm thủ công
                    </button>
                </div>
                
                <div id="warehouse_warning" class="m-4 text-orange-600 bg-orange-50 p-3 rounded-lg text-sm font-medium flex items-center border border-orange-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Vui lòng chọn Nhà Kho ở phía trên để tải dữ liệu tồn kho tổng.
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="main_table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-[45%]">Sản phẩm cần lấy</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-[15%]">Tổng tồn Kho</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-[30%]">SL Yêu cầu / Đề xuất kệ</th>
                                <th class="px-5 py-3 w-[10%] text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <!-- JS sẽ chèn các thẻ <tbody> vào ngay đây -->
                    </table>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('outbounds.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50 transition shadow-sm">Hủy bỏ</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-bold shadow-md hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-200 text-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    {{ $isEdit ? 'Lưu Thay Đổi (Cập nhật)' : 'Lưu Phiếu Xuất (Nháp)' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let globalInventory = []; 
        let uniqueProducts = {}; 
        let globalItemIndex = 0; 

        let initialItems = @json($initialItems);
        if(typeof initialItems === 'object' && initialItems !== null && !Array.isArray(initialItems)) {
            initialItems = Object.values(initialItems);
        }
        
        let isFirstLoad = true;

        const warehouseSelect = document.getElementById('warehouse_id');
        const typeSelect = document.getElementById('type');
        const orderSelect = document.getElementById('order_id');
        const mainTable = document.getElementById('main_table');
        const btnAddBlock = document.getElementById('btn_add_block');
        const warning = document.getElementById('warehouse_warning');

        // Logic ẩn/hiện mục đích xuất
        function toggleType() {
            if(typeSelect.value === 'sales') {
                document.getElementById('div_order').classList.remove('hidden');
                document.getElementById('div_reason').classList.add('hidden');
            } else {
                document.getElementById('div_order').classList.add('hidden');
                document.getElementById('div_reason').classList.remove('hidden');
                // Clear the order selection if switching away from sales
                orderSelect.value = '';
            }
        }
        typeSelect.addEventListener('change', toggleType);
        toggleType();

        // ======= TẢI TỒN KHO TỪ WAREHOUSE =======
        warehouseSelect.addEventListener('change', async function() {
            const wId = this.value;
            // Clear current list unless it's edit mode first load
            if(!isFirstLoad) {
                mainTable.querySelectorAll('tbody').forEach(tb => tb.remove());
                orderSelect.value = ''; // Reset order to avoid confusion
            }
            
            uniqueProducts = {};

            if(!wId) {
                warning.style.display = 'flex';
                btnAddBlock.disabled = true;
                return;
            }

            warning.style.display = 'none';
            btnAddBlock.disabled = false;
            btnAddBlock.innerHTML = '⏳ Đang tải kho...';
            
            try {
                const response = await fetch(`/api/inventory/${wId}`);
                globalInventory = await response.json();
                
                globalInventory.forEach(inv => {
                    let available = inv.quantity - inv.reserved_quantity;
                    if(available > 0) {
                        if(!uniqueProducts[inv.product_id]) {
                            uniqueProducts[inv.product_id] = {
                                id: inv.product_id,
                                name: inv.product.name,
                                total: 0,
                                stocks: []
                            };
                        }
                        uniqueProducts[inv.product_id].total += available;
                        uniqueProducts[inv.product_id].stocks.push({
                            location_id: inv.location_id,
                            location_name: inv.location.name,
                            batch_id: inv.batch_id,
                            batch_code: inv.batch ? inv.batch.batch_code : null,
                            available: available
                        });
                    }
                });

                btnAddBlock.innerHTML = '+ Thêm Sản Phẩm thủ công';

                if (isFirstLoad && initialItems && initialItems.length > 0) {
                    let groupedOld = {};
                    initialItems.forEach(item => {
                        if (!groupedOld[item.product_id]) {
                            groupedOld[item.product_id] = { total_req: 0, details: [] };
                        }
                        groupedOld[item.product_id].total_req += parseInt(item.quantity);
                        groupedOld[item.product_id].details.push(item);
                    });

                    for (let pid in groupedOld) {
                        addBlock(pid, groupedOld[pid].total_req, groupedOld[pid].details);
                    }
                    isFirstLoad = false; 
                } 
            } catch(e) {
                console.error("Lỗi API kho", e);
                alert("Lỗi tải dữ liệu tồn kho!");
                btnAddBlock.innerHTML = '+ Thêm Sản Phẩm thủ công';
            }
        });

        // ======= TỰ ĐỘNG TẢI ORDER ITEMS KHI CHỌN ORDER =======
        orderSelect.addEventListener('change', async function() {
            const oId = this.value;
            const wId = warehouseSelect.value;

            if (!wId) {
                alert('⚠️ Vui lòng chọn "Nhà Kho Xuất Hàng" trước để hệ thống lấy dữ liệu tồn kho tính toán!');
                this.value = ''; // Reset select
                return;
            }

            // Xóa danh sách SP hiện tại trên form
            mainTable.querySelectorAll('tbody').forEach(tb => tb.remove());

            if (!oId) return;

            try {
                // Gọi API lấy Items của Order
                const response = await fetch(`/api/orders/${oId}/items`);
                console.log("API /api/orders/{id}/items response:", response);
                const orderItems = await response.json();

                if (orderItems.length === 0) {
                    alert('Đơn hàng này không có sản phẩm nào hợp lệ!');
                    return;
                }

                // Chèn từng item của order vào Form
                orderItems.forEach(item => {
                    addBlock(item.product_id, item.quantity);
                });

            } catch (e) {
                console.error("Lỗi tải API Đơn hàng", e);
                alert("Không thể lấy dữ liệu sản phẩm từ đơn hàng này.");
            }
        });

        // ==========================================

        function generateProductOptions(selectedPid = null) {
            let html = '<option value="">-- Chọn Sản Phẩm cần xuất --</option>';
            for (let pid in uniqueProducts) {
                let p = uniqueProducts[pid];
                let sel = (pid == selectedPid) ? 'selected' : '';
                html += `<option value="${p.id}" ${sel}>${p.name} (Tồn khả dụng: ${p.total})</option>`;
            }
            if (selectedPid && !uniqueProducts[selectedPid]) {
                html += `<option value="${selectedPid}" selected class="text-red-500 font-bold">⚠️ SP ID ${selectedPid} (Hết hàng)</option>`;
            }
            return html;
        }

        // THÊM 1 KHỐI SẢN PHẨM
        function addBlock(prePid = null, preQty = null, preDetails = null) {
            let tbody = document.createElement('tbody');
            tbody.className = "border-b-[12px] border-white shadow-sm"; 
            
            let maxTotal = prePid && uniqueProducts[prePid] ? uniqueProducts[prePid].total : 0;
            let displayMax = prePid ? maxTotal : '0';

            tbody.innerHTML = `
                <tr class="bg-gray-100 hover:bg-gray-200 transition">
                    <td class="px-5 py-3">
                        <select class="master-product block w-full px-4 py-2 bg-white border border-gray-300 rounded-lg sm:text-sm font-bold text-gray-800 focus:ring-indigo-500 cursor-pointer shadow-sm">
                            ${generateProductOptions(prePid)}
                        </select>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="master-max inline-flex items-center justify-center px-4 py-1.5 text-sm font-black bg-indigo-600 text-white rounded-full shadow-sm">
                            ${displayMax}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="relative">
                            <input type="number" class="master-qty block w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg sm:text-sm font-bold text-gray-900 focus:ring-indigo-500 shadow-sm" placeholder="Nhập tổng SL cần xuất..." value="${preQty || ''}" min="1" max="${maxTotal}">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm font-medium">SP</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <button type="button" class="btn-remove-block text-gray-500 hover:text-red-600 hover:bg-red-100 p-2 rounded-lg transition" title="Xóa toàn bộ SP này">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                </tr>
            `;
            
            mainTable.appendChild(tbody);

            let prodSelect = tbody.querySelector('.master-product');
            let maxSpan = tbody.querySelector('.master-max');
            let qtyInput = tbody.querySelector('.master-qty');

            // EVENT: Đổi sản phẩm bằng tay
            prodSelect.addEventListener('change', function() {
                let pid = this.value;
                if(pid && uniqueProducts[pid]) {
                    maxSpan.innerText = uniqueProducts[pid].total;
                    qtyInput.max = uniqueProducts[pid].total;
                } else {
                    maxSpan.innerText = '0';
                    qtyInput.max = '';
                }
                qtyInput.value = '';
                tbody.querySelectorAll('.detail-row').forEach(row => row.remove());
                qtyInput.focus();
            });

            // EVENT: Gõ số lượng -> CẬP NHẬT ALLOCATE
            qtyInput.addEventListener('input', function() {
                let pid = prodSelect.value;
                let reqQty = parseInt(this.value);
                
                tbody.querySelectorAll('.detail-row').forEach(row => row.remove());

                if(!pid || isNaN(reqQty) || reqQty <= 0) return;

                let maxT = parseInt(this.max);
                if (maxT && reqQty > maxT) {
                    alert('Số lượng yêu cầu vượt quá tổng tồn kho đang có trong KHO NÀY!');
                    this.value = maxT;
                    reqQty = maxT;
                }

                autoAllocate(tbody, pid, reqQty);
            });

            // Nếu đang trong chế độ Edit (có truyền preDetails chi tiết)
            if (preDetails) {
                preDetails.forEach(dt => {
                    renderDetailRow(tbody, prePid, dt.location_id, dt.batch_id, dt.quantity);
                });
            } 
            // NẾU TỰ ĐỘNG ĐƯỢC GỌI TỪ EVENT CHỌN ORDER (Có prePid, preQty nhưng không có preDetail)
            else if (prePid && preQty) {
                // Kích hoạt ngay autoAllocate cho dòng này
                autoAllocate(tbody, prePid, preQty);
            }

            // Xóa Block
            tbody.querySelector('.btn-remove-block').addEventListener('click', function() {
                tbody.remove();
            });
        }

        // THUẬT TOÁN TỰ ĐỘNG PHÂN BỔ (FEFO/FIFO)
        function autoAllocate(tbody, pid, reqQty) {
            let prod = uniqueProducts[pid];
            if(!prod) return;

            let remaining = reqQty;

            prod.stocks.forEach(stock => {
                if(remaining <= 0) return;
                
                let take = Math.min(stock.available, remaining);
                renderDetailRow(tbody, pid, stock.location_id, stock.batch_id, take);
                remaining -= take;
            });

            if (remaining > 0) {
                let trAlert = document.createElement('tr');
                trAlert.className = 'detail-row bg-red-50';
                trAlert.innerHTML = `<td colspan="4" class="px-5 py-2 text-red-600 font-bold text-sm text-center">⚠️ Kho bị thiếu ${remaining} SP để đáp ứng đủ yêu cầu này! Vui lòng kiểm tra lại.</td>`;
                tbody.appendChild(trAlert);
            }
        }

        // RENDER 1 DÒNG ĐỀ XUẤT
        function renderDetailRow(tbody, pid, locId, batchId, qty) {
            let prod = uniqueProducts[pid];
            let locOptions = '';
            
            if (prod) {
                locOptions = prod.stocks.map(s => {
                    let sel = (s.location_id == locId && s.batch_id == batchId) ? 'selected' : '';
                    let bTxt = s.batch_code ? ` (Lô: ${s.batch_code})` : '';
                    return `<option value="${s.location_id}_${s.batch_id||''}" data-lid="${s.location_id}" data-bid="${s.batch_id||''}" data-max="${s.available}" ${sel}>Kệ: ${s.location_name}${bTxt} - Tồn: ${s.available}</option>`;
                }).join('');
            } else {
                locOptions = `<option value="${locId}_${batchId||''}" selected>Kệ ID: ${locId} (Dữ liệu cũ)</option>`;
            }

            let tr = document.createElement('tr');
            tr.className = "detail-row bg-white border-t border-gray-100";
            tr.innerHTML = `
                <td colspan="2" class="px-5 py-3 pl-12 relative">
                    <div class="absolute left-6 top-1/2 -mt-4 text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    </div>
                    <select class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 font-medium text-indigo-900 bg-indigo-50/30" onchange="updateHiddenFields(this)">
                        ${locOptions}
                    </select>

                    <input type="hidden" name="items[${globalItemIndex}][product_id]" value="${pid}">
                    <input type="hidden" name="items[${globalItemIndex}][location_id]" class="h-lid" value="${locId}">
                    <input type="hidden" name="items[${globalItemIndex}][batch_id]" class="h-bid" value="${batchId || ''}">
                </td>
                
                <td class="px-5 py-3">
                    <input type="number" name="items[${globalItemIndex}][quantity]" value="${qty}" min="1" class="block w-full text-sm border-indigo-200 rounded-md text-center font-bold text-red-600 focus:ring-red-500 bg-red-50 shadow-inner" required>
                </td>
                
                <td class="px-5 py-3 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-gray-300 hover:text-red-500 p-1 rounded transition" title="Xóa dòng đề xuất này">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </td>
            `;
            
            tbody.appendChild(tr);
            globalItemIndex++;
        }

        window.updateHiddenFields = function(selectEl) {
            let opt = selectEl.options[selectEl.selectedIndex];
            let container = selectEl.closest('tr');
            
            container.querySelector('.h-lid').value = opt.getAttribute('data-lid');
            container.querySelector('.h-bid').value = opt.getAttribute('data-bid') || '';
            
            let qtyInput = container.querySelector('input[type="number"]');
            let max = opt.getAttribute('data-max');
            if(max) qtyInput.setAttribute('max', max);
        };

        btnAddBlock.addEventListener('click', () => addBlock());
        
        // Trigger default load nếu đang ở màn Edit
        if (warehouseSelect.value) {
            warehouseSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection