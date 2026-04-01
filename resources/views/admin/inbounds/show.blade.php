@extends('layouts.admin')

@section('content')
<div class="max-w-360 mx-auto space-y-6">
    <!-- Nút điều hướng -->
    <div class="mb-2">
        <a href="{{ route('inbounds.index') }}" class="inline-flex items-center text-slate-500 hover:text-indigo-600 font-bold transition text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            QUAY LẠI DANH SÁCH
        </a>
    </div>

    <!-- KHU VỰC HIỂN THỊ THÔNG BÁO LỖI / THÀNH CÔNG -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800">Không thể lưu phiếu nhập, vui lòng sửa các lỗi sau:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md shadow-sm">
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
            <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Header Thông tin phiếu -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center">
                    📥 Phiếu Nhập: <span class="text-indigo-600 ml-2">INB-{{ str_pad($inbound->id, 5, '0', STR_PAD_LEFT) }}</span>
                </h2>
                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm bg-white px-4 py-2.5 rounded-lg border border-slate-200 shadow-sm">
                    <span class="text-slate-500">Nhà cung cấp: <strong class="text-slate-900">{{ $inbound->supplier->name ?? 'N/A' }}</strong></span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-500">Ngày lập: <strong class="text-slate-900">{{ $inbound->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }} (VN)</strong></span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-500">Người lập: <strong class="text-slate-900">{{ $inbound->staff->full_name ?? 'N/A' }}</strong></span>
                </div>
            </div>
            <div class="shrink-0">
                @if($inbound->status === 'pending') 
                    <span class="px-5 py-2.5 text-sm bg-amber-100 text-amber-800 rounded-lg font-bold shadow-sm border border-amber-200">⏳ Chờ Nhập Kho (Bản Nháp)</span>
                @elseif($inbound->status === 'completed') 
                    <span class="px-5 py-2.5 text-sm bg-emerald-100 text-emerald-800 rounded-lg font-bold shadow-sm border border-emerald-200">✔ Đã Cất Hàng Vào Kho</span>
                @else 
                    <span class="px-5 py-2.5 text-sm bg-rose-100 text-rose-800 rounded-lg font-bold shadow-sm border border-rose-200">❌ Đã Hủy Phiếu</span> 
                @endif
            </div>
        </div>
    </div>

    @if($inbound->status === 'pending')
    <!-- BƯỚC 1: Form thêm sản phẩm nhập (Dạng Toolbar Ngang) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <h3 class="text-base font-extrabold text-slate-800 mb-3 flex items-center">
            <span class="bg-indigo-600 text-white rounded w-6 h-6 inline-flex items-center justify-center text-xs mr-2 shadow-sm">1</span>
            Thêm sản phẩm vào phiếu
        </h3>
        <form action="{{ route('inbounds.addItem', $inbound->id) }}" method="POST" class="flex flex-wrap lg:flex-nowrap gap-3 items-end">
            @csrf
            <div class="flex-1 min-w-62.5">
                <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-500 mb-1.5">Sản phẩm <span class="text-rose-500">*</span></label>
                <select name="product_id" required class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer font-semibold text-sm py-2">
                    <option value="">-- Tìm và chọn sản phẩm --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                            {{ $prod->name }} (Mã: {{ $prod->id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-28">
                <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-500 mb-1.5">SL <span class="text-rose-500">*</span></label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required class="block w-full border-slate-300 rounded-lg shadow-sm text-center font-bold text-indigo-700 focus:ring-indigo-500 text-sm py-2">
            </div>
            <div class="w-full sm:w-40">
                <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-500 mb-1.5">Giá nhập (VNĐ) <span class="text-rose-500">*</span></label>
                <input type="number" name="price" min="0" value="{{ old('price', 0) }}" required class="block w-full border-slate-300 rounded-lg shadow-sm text-right font-bold text-slate-800 focus:ring-indigo-500 text-sm py-2">
            </div>
            <button type="submit" class="w-full lg:w-auto bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-indigo-700 transition text-sm whitespace-nowrap">
                + Thêm dòng
            </button>
        </form>
    </div>
    @endif

    <!-- BƯỚC 2: Bảng sản phẩm chi tiết & Gán Lô/Vị trí -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-white flex justify-between items-center">
            <h3 class="text-base font-extrabold text-slate-800 flex items-center">
                <span class="bg-indigo-600 text-white rounded w-6 h-6 inline-flex items-center justify-center text-xs mr-2 shadow-sm">2</span>
                Chi tiết Nhập kho & Khai báo Lô (Batch)
            </h3>
            @if($inbound->status === 'pending')
                <span class="text-xs text-slate-500 italic font-medium">* Dữ liệu lô và kệ được tự động lưu tạm trên trình duyệt</span>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-[22%]">Sản phẩm</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-[20%]">Khối lượng & Giá nhập</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-[23%]">Vị trí cất hàng</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider w-[25%]">Khai báo Lô & Date (Tùy chọn)</th>
                        @if($inbound->status === 'pending') 
                            <th class="px-5 py-3 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider w-[10%]">Thao tác</th> 
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($inbound->items ?? [] as $item)
                        <!-- ĐÃ FIX: Thêm ID để Anchor URL Scroll về đúng vị trí -->
                        <tr id="item-{{ $item->id }}" class="hover:bg-slate-50/50 transition-colors align-top group">
                            
                            <!-- CỘT 1: SẢN PHẨM -->
                            <td class="px-5 py-4">
                                <p class="text-sm font-extrabold text-slate-800">{{ $item->product->name ?? 'N/A' }}</p>
                                <p class="text-[11px] text-slate-500 mt-1 font-mono bg-slate-100 px-1.5 py-0.5 inline-block rounded border border-slate-200">Mã: SP-{{ $item->product_id }}</p>
                            </td>
                            
                            @if($inbound->status === 'pending')
                                <!-- CỘT 2: KHỐI LƯỢNG & GIÁ -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-bold text-slate-500 w-8">SL:</span>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" form="update-{{$item->id}}" required min="1" class="w-full max-w-25 border-slate-300 rounded text-center text-sm shadow-inner focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-700 py-1.5 px-2">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-500 w-8">Giá:</span>
                                        <input type="number" name="price" value="{{ round($item->price) }}" form="update-{{$item->id}}" required min="0" class="w-full max-w-25 border-slate-300 rounded text-right text-sm shadow-inner focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 font-medium py-1.5 px-2">
                                    </div>
                                </td>
                                
                                <!-- CỘT 3: VỊ TRÍ KỆ -->
                                <td class="px-5 py-4">
                                    <select name="assignments[{{ $item->id }}][location_id]" form="complete-form" required class="auto-save-input block w-full border-slate-300 rounded shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-900 bg-white py-1.5 px-2">
                                        <option value="">-- Chọn Kệ thực tế --</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old("assignments.{$item->id}.location_id") == $location->id ? 'selected' : '' }}>
                                                [{{ $location->warehouse->name ?? '' }}] - {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- CỘT 4: THÔNG TIN LÔ HÀNG -->
                                <td class="px-5 py-4">
                                    <div class="space-y-2">
                                        <input type="text" name="assignments[{{ $item->id }}][batch_code]" value="{{ old("assignments.{$item->id}.batch_code") }}" form="complete-form" placeholder="Mã Lô / Batch Code" class="auto-save-input block w-full border-slate-300 rounded shadow-sm text-xs font-mono focus:ring-indigo-500 uppercase py-1.5 px-2 placeholder-slate-400">
                                        <div class="flex gap-2">
                                            <input type="date" name="assignments[{{ $item->id }}][manufacture_date]" value="{{ old("assignments.{$item->id}.manufacture_date") }}" form="complete-form" title="Ngày sản xuất" class="auto-save-input block w-1/2 border-slate-300 rounded shadow-sm text-xs text-slate-600 focus:ring-indigo-500 py-1.5 px-2">
                                            <input type="date" name="assignments[{{ $item->id }}][expiry_date]" value="{{ old("assignments.{$item->id}.expiry_date") }}" form="complete-form" title="Hạn sử dụng" class="auto-save-input block w-1/2 border-slate-300 rounded shadow-sm text-xs text-slate-600 focus:ring-indigo-500 py-1.5 px-2">
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- CỘT 5: THAO TÁC -->
                                <td class="px-5 py-4 text-center">
                                    <div class="flex flex-col gap-2 items-center">
                                        <!-- Nút cập nhật sử dụng action có Hash Anchor -->
                                        <button type="submit" form="update-{{$item->id}}" class="w-full text-blue-600 hover:text-white border border-blue-200 hover:bg-blue-600 font-bold text-xs px-2 py-1.5 rounded transition shadow-sm bg-blue-50">Lưu SL/Giá</button>
                                        <button type="submit" form="del-{{$item->id}}" class="w-full text-rose-500 hover:text-white border border-rose-200 hover:bg-rose-500 font-bold text-xs px-2 py-1.5 rounded transition shadow-sm bg-rose-50" onclick="return confirm('Xóa sản phẩm này khỏi phiếu nhập?');">Xóa Dòng</button>
                                    </div>
                                </td>
                            @else
                                <!-- TRẠNG THÁI ĐÃ HOÀN TẤT (CHỈ XEM) -->
                                <td class="px-5 py-4">
                                    <div class="text-lg font-black text-indigo-700">{{ $item->quantity }} <span class="text-xs text-slate-500 font-medium">đơn vị</span></div>
                                    <div class="text-xs font-bold text-slate-600 mt-1">{{ number_format($item->price) }} đ/sp</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center text-xs text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded border border-emerald-200 shadow-sm">
                                        ✔ Đã cất vào kho
                                    </span>
                                </td>
                                <td class="px-5 py-4" colspan="2">
                                    @if(isset($item->batch) && $item->batch)
                                        <div class="bg-slate-50 p-2 rounded border border-slate-200 flex flex-wrap gap-4 text-xs">
                                            <div>
                                                <span class="text-slate-500 font-semibold">Lô:</span>
                                                <span class="font-mono font-bold text-indigo-800">{{ $item->batch->batch_code }}</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-500 font-semibold">NSX:</span>
                                                <span class="font-medium text-slate-800">{{ $item->batch->manufacture_date ? \Carbon\Carbon::parse($item->batch->manufacture_date)->format('d/m/Y') : 'N/A' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-500 font-semibold">HSD:</span>
                                                <span class="font-bold {{ $item->batch->expiry_date && \Carbon\Carbon::parse($item->batch->expiry_date)->isPast() ? 'text-rose-600' : 'text-slate-800' }}">
                                                    {{ $item->batch->expiry_date ? \Carbon\Carbon::parse($item->batch->expiry_date)->format('d/m/Y') : 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400 italic">Sản phẩm không quản lý theo lô.</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-100 text-slate-400 mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-800 mb-1">Phiếu nhập đang trống</h3>
                                <p class="text-slate-500 font-medium text-sm">Hãy thêm sản phẩm ở Bước 1 phía trên.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inbound->status === 'pending' && count($inbound->items ?? []) > 0)
            <div class="p-5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-end items-center gap-3">
                <button type="submit" form="cancel-form" class="w-full sm:w-auto bg-white border border-rose-200 text-rose-600 px-6 py-2.5 rounded-lg font-bold shadow-sm hover:bg-rose-50 transition text-sm" onclick="return confirm('Hủy bỏ toàn bộ phiếu nhập này? Mọi dữ liệu sẽ bị xóa.');">❌ HỦY PHIẾU NHÁP</button>
                
                <button type="submit" form="complete-form" class="w-full sm:w-auto bg-emerald-600 text-white px-8 py-2.5 rounded-lg font-extrabold hover:bg-emerald-700 shadow-md transition focus:ring-4 focus:ring-emerald-200 flex items-center justify-center text-base" onclick="return confirm('Hàng hóa sẽ được CỘNG THẲNG VÀO KHO và KHÔNG THỂ SỬA ĐƯỢC NỮA. Xác nhận chốt phiếu?');">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    CHỐT PHIẾU & NHẬP KHO
                </button>
            </div>
        @endif

        <!-- ================= CÁC FORM NGẦM ĐỂ XỬ LÝ DỮ LIỆU ================= -->
        <form id="complete-form" action="{{ route('inbounds.complete', $inbound->id) }}" method="POST" class="hidden">@csrf</form>
        <form id="cancel-form" action="{{ route('inbounds.cancel', $inbound->id) }}" method="POST" class="hidden">@csrf</form>
        
        @if($inbound->status === 'pending')
            @foreach($inbound->items ?? [] as $item)
                <form id="update-{{$item->id}}" action="{{ route('inbounds.items.update', [$inbound->id, $item->id]) }}#item-{{$item->id}}" method="POST" class="hidden">@csrf @method('PUT')</form>
                <form id="del-{{$item->id}}" action="{{ route('inbounds.removeItem', [$inbound->id, $item->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
            @endforeach
        @endif
        <!-- ================================================================= -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. HIỆU ỨNG HIGHLIGHT KHI LƯU CẬP NHẬT HOẶC XÓA XONG
    // Dựa vào hash trên URL để đổi màu background dòng vừa thao tác
    if (window.location.hash) {
        const targetRow = document.querySelector(window.location.hash);
        if (targetRow) {
            targetRow.classList.add('bg-indigo-50', 'transition-colors', 'duration-1000');
            setTimeout(() => {
                targetRow.classList.remove('bg-indigo-50');
            }, 2500);
        }
    }

    // 2. CHỐNG MẤT DỮ LIỆU KỆ / LÔ BẰNG SESSION STORAGE
    const inboundId = {{ $inbound->id }};
    const autoSaveInputs = document.querySelectorAll('.auto-save-input');
    const hasValidationError = {{ $errors->any() ? 'true' : 'false' }};

    autoSaveInputs.forEach(input => {
        const storageKey = `inb_${inboundId}_${input.name}`;
        
        // Phục hồi dữ liệu cũ nếu ô đang rỗng (không có old data từ Laravel)
        const savedValue = sessionStorage.getItem(storageKey);
        if (!hasValidationError && savedValue !== null && input.value === '') {
            input.value = savedValue;
        }

        // Tự động lưu ngầm mỗi khi user gõ hoặc chọn
        input.addEventListener('change', function() {
            sessionStorage.setItem(storageKey, this.value);
        });
        input.addEventListener('keyup', function() {
            sessionStorage.setItem(storageKey, this.value);
        });
    });

    // 3. XÓA BỘ NHỚ TẠM KHI CHỐT PHIẾU THÀNH CÔNG
    @if($inbound->status === 'completed' || $inbound->status === 'cancelled')
        Object.keys(sessionStorage).forEach(key => {
            if (key.startsWith(`inb_${inboundId}_`)) {
                sessionStorage.removeItem(key);
            }
        });
    @endif
});
</script>
@endsection