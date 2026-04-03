@extends('layouts.admin')

@section('content')
    <div class="max-w-360 mx-auto space-y-6 pb-20">
        <!-- Điều hướng -->
        <div class="mb-2">
            <a href="{{ route('stock_takes.index') }}"
                class="inline-flex items-center text-slate-500 hover:text-indigo-600 font-bold transition text-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                QUAY LẠI DANH SÁCH
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                <ul class="text-sm text-red-700 list-disc list-inside font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md shadow-sm font-bold text-emerald-800">
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-md shadow-sm font-bold text-rose-800">
                {{ session('error') }}</div>
        @endif

        <!-- Header Phiếu -->
        <div
            class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">
                    📋 Phiếu Kiểm Kê: <span class="text-indigo-600">{{ $stockTake->code }}</span>
                </h2>
                <div class="mt-2 text-sm text-slate-600">
                    <span class="font-bold mr-4">🏠 Kho: {{ $stockTake->warehouse->name ?? 'N/A' }}</span>
                    <span>👤 Người tạo: {{ $stockTake->staff->username ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-2 w-full md:w-auto">
                @if ($stockTake->status == 'draft')
                    <form action="{{ route('stock_takes.start', $stockTake->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold shadow-md hover:bg-indigo-700 transition"
                            onclick="return confirm('Hệ thống sẽ chụp lại Tồn kho hiện tại. Bắt đầu đếm?');">
                            ▶ BẮT ĐẦU ĐẾM (SNAPSHOT)
                        </button>
                    </form>
                @elseif($stockTake->status == 'counting')
                    <span
                        class="px-5 py-2.5 text-sm bg-amber-100 text-amber-800 rounded-lg font-bold border border-amber-200 text-center">⏳
                        ĐANG ĐẾM (COUNTING)</span>
                @elseif($stockTake->status == 'completed')
                    <span
                        class="px-5 py-2.5 text-sm bg-emerald-100 text-emerald-800 rounded-lg font-bold border border-emerald-200 text-center">✔
                        ĐÃ CHỐT SỔ KHO</span>
                @endif
            </div>
        </div>

        <!-- Bảng Làm Việc -->
        @if ($stockTake->status != 'draft')
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="font-bold text-slate-700">Danh sách các mặt hàng trong Kho</h3>
                    @if ($stockTake->status == 'counting')
                        <p class="text-xs text-rose-500 font-bold italic">* Nhập số lượng thực tế đếm được. Hệ thống tự tính
                            độ lệch.</p>
                    @endif
                </div>

                <form id="bulk-count-form" action="{{ route('stock_takes.updateBulk', $stockTake->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Input ẩn lưu trữ hành động (Lưu tạm hay Hoàn tất) -->
                    <input type="hidden" name="action" id="form-action" value="save">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Sản phẩm</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kệ (Vị trí)
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Lô / HSD</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-bold text-indigo-500 uppercase bg-indigo-50">
                                        Tồn HT</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-bold text-emerald-600 uppercase bg-emerald-50 w-32">
                                        Thực Tế</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-rose-500 uppercase bg-rose-50">
                                        Độ Lệch</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-48">Lý do
                                        lệch</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach ($stockTake->items as $item)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3 text-sm font-bold text-slate-800">
                                            {{ $item->product->name ?? 'N/A' }}<br>
                                            <span
                                                class="text-[10px] text-slate-400 font-mono font-normal">SP-{{ $item->product_id }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700 font-bold">
                                            {{ $item->location->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-xs text-slate-600">
                                            @if ($item->batch)
                                                <span class="font-bold">Lô: {{ $item->batch->batch_code }}</span>
                                            @else
                                                <span class="text-slate-400 italic">Không có</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center bg-slate-50/50">
                                            <span
                                                class="font-black text-lg text-indigo-400">{{ $item->expected_quantity }}</span>
                                        </td>

                                        <td class="px-4 py-3 text-center bg-emerald-50/30">
                                            @if ($stockTake->status == 'counting')
                                                <input type="number" name="items[{{ $item->id }}][counted_quantity]"
                                                    value="{{ $item->counted_quantity ?? $item->expected_quantity }}"
                                                    min="0" data-id="{{ $item->id }}"
                                                    data-expected="{{ $item->expected_quantity }}"
                                                    class="count-input w-full border-emerald-200 rounded text-center font-black text-emerald-700 h-9.5">
                                            @else
                                                <span
                                                    class="font-black text-lg text-emerald-600">{{ $item->counted_quantity ?? '-' }}</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center bg-rose-50/30">
                                            <span id="variance-{{ $item->id }}"
                                                class="font-bold text-lg {{ $item->variance > 0 ? 'text-emerald-600' : ($item->variance < 0 ? 'text-rose-600' : 'text-slate-400') }}">
                                                @if ($item->counted_quantity !== null)
                                                    {{ $item->variance > 0 ? '+' : '' }}{{ $item->variance }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </td>

                                        <td class="px-4 py-3">
                                            @if ($stockTake->status == 'counting')
                                                <input type="text" name="items[{{ $item->id }}][reason]"
                                                    value="{{ $item->reason }}"
                                                    class="w-full border-slate-200 rounded text-xs h-9.5"
                                                    placeholder="VD: Hàng hỏng...">
                                            @else
                                                <span class="text-xs text-slate-500">{{ $item->reason ?? '' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($stockTake->status == 'counting')
                        <div class="p-4 bg-slate-100 border-t border-slate-200 flex justify-end items-center gap-3">
                            <button type="button" onclick="submitForm('save')"
                                class="bg-white border-2 border-indigo-200 text-indigo-600 font-bold px-6 py-2.5 rounded-lg shadow-sm hover:bg-indigo-50 transition">
                                💾 LƯU TẠM THỜI
                            </button>

                            <button type="button" onclick="submitForm('complete')"
                                class="bg-emerald-600 text-white font-extrabold px-8 py-2.5 rounded-lg shadow-md hover:bg-emerald-700 transition">
                                ✔ HOÀN TẤT & ĐIỀU CHỈNH KHO
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.count-input').forEach(input => {
                input.addEventListener('input', function() {
                    let expected = parseInt(this.dataset.expected) || 0;
                    let counted = parseInt(this.value);
                    let varianceTd = document.getElementById('variance-' + this.dataset.id);

                    if (isNaN(counted)) {
                        varianceTd.textContent = '-';
                        varianceTd.className = 'font-bold text-lg text-slate-400';
                        return;
                    }
                    let variance = counted - expected;
                    varianceTd.textContent = (variance > 0 ? '+' : '') + variance;
                    varianceTd.className = 'font-bold text-lg ' + (variance > 0 ?
                        'text-emerald-600' : (variance < 0 ? 'text-rose-600' : 'text-slate-400')
                        );
                });
            });
        });

        function submitForm(actionType) {
            // Đổi giá trị của thẻ input hidden 'action'
            document.getElementById('form-action').value = actionType;

            if (actionType === 'complete') {
                let inputs = document.querySelectorAll('.count-input');
                let hasEmpty = false;
                inputs.forEach(input => {
                    if (input.value === '') hasEmpty = true;
                });

                if (hasEmpty) {
                    alert("Lỗi: Có mặt hàng chưa được nhập số lượng đếm thực tế. Vui lòng nhập số 0 nếu hết hàng.");
                    return; // Chặn không cho submit
                }

                if (!confirm(
                        'Dữ liệu bạn vừa nhập sẽ được lưu lại, sau đó Lượng Tồn Kho Thực Tế sẽ bị ĐIỀU CHỈNH khớp với số này.\n\nBạn có chắc chắn muốn chốt sổ?'
                        )) {
                    return; // Hủy submit
                }
            }

            // Tiến hành submit form
            document.getElementById('bulk-count-form').submit();
        }
    </script>
@endsection
