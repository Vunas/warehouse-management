@extends('layouts.admin')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 42px !important; border-color: #cbd5e1 !important; border-radius: 0.5rem !important; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { font-weight: 700; color: #1e293b; }
</style>

<div class="max-w-[90rem] mx-auto space-y-6 pb-20">
    <div class="mb-2">
        <a href="{{ route('transfers.index') }}" class="inline-flex items-center text-slate-500 hover:text-indigo-600 font-bold transition text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            QUAY LẠI DANH SÁCH
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
            <ul class="text-sm text-red-700 list-disc list-inside font-bold">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md shadow-sm font-bold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm font-bold text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-slate-50 border-b border-slate-100">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">
                    🔄 Phiếu Luân chuyển: <span class="text-indigo-600 ml-1">#TRF-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</span>
                </h2>
                <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                    <div class="bg-white border border-rose-200 px-4 py-2 rounded-lg shadow-sm flex items-center">
                        <span class="text-[10px] font-black text-rose-500 uppercase tracking-wider mr-3 bg-rose-50 px-2 py-1 rounded">RÚT TỪ KHO</span>
                        <span class="font-bold text-slate-800 text-base">{{ $transfer->fromWarehouse->name ?? 'N/A' }}</span>
                    </div>
                    <svg class="w-6 h-6 text-slate-300 hidden sm:block mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    <div class="bg-white border border-emerald-200 px-4 py-2 rounded-lg shadow-sm flex items-center">
                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-wider mr-3 bg-emerald-50 px-2 py-1 rounded">CẤT VÀO KHO</span>
                        <span class="font-bold text-slate-800 text-base">{{ $transfer->toWarehouse->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                @if($transfer->status === 'pending') 
                    <span class="px-5 py-2.5 text-sm bg-amber-100 text-amber-800 rounded-lg font-bold shadow-sm border border-amber-200">⏳ Đang lấy hàng (Nháp)</span>
                @elseif($transfer->status === 'completed') 
                    <span class="px-5 py-2.5 text-sm bg-emerald-100 text-emerald-800 rounded-lg font-bold shadow-sm border border-emerald-200">✔ Đã Luân Chuyển Xong</span>
                @else 
                    <span class="px-5 py-2.5 text-sm bg-rose-100 text-rose-800 rounded-lg font-bold shadow-sm border border-rose-200">❌ Đã Hủy</span> 
                @endif
            </div>
        </div>
    </div>

    @if($transfer->status === 'pending')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-base font-extrabold text-slate-800 mb-4 flex items-center">
            <span class="bg-indigo-600 text-white rounded-md w-7 h-7 inline-flex items-center justify-center text-sm mr-3 shadow-sm">1</span>
            Thêm sản phẩm cần chuyển (Lên kế hoạch dự tính)
        </h3>
        <form action="{{ route('transfers.items.add', $transfer->id) }}" method="POST" class="flex flex-wrap lg:flex-nowrap gap-4 items-end bg-slate-50/50 p-5 rounded-xl border border-slate-100">
            @csrf
            <div class="flex-1 min-w-[300px]">
                <label class="block text-xs uppercase tracking-wider font-bold text-slate-500 mb-2">Sản phẩm đang có ở Kho Nguồn <span class="text-rose-500">*</span></label>
                <select name="product_id" required class="select2 block w-full">
                    <option value="">-- Gõ tên hoặc mã sản phẩm để tìm --</option>
                    @foreach($productsInStock as $p)
                        <option value="{{ $p->product_id }}">
                            {{ $p->product->name ?? 'N/A' }} (Mã: SP-{{ $p->product_id }}) - Tổng hiện có: {{ $p->total_available }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <label class="block text-xs uppercase tracking-wider font-bold text-slate-500 mb-2">Số lượng <span class="text-rose-500">*</span></label>
                <input type="number" name="quantity" min="1" value="1" required class="block w-full border-slate-300 rounded-lg shadow-sm text-center font-black text-indigo-700 py-2.5 h-[42px] focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="w-full lg:w-auto bg-indigo-600 text-white px-8 py-2.5 rounded-lg font-bold shadow-md hover:bg-indigo-700 transition whitespace-nowrap h-[42px]">
                + Thêm vào phiếu
            </button>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-white">
            <h3 class="text-base font-extrabold text-slate-800 flex items-center">
                <span class="bg-indigo-600 text-white rounded-md w-7 h-7 inline-flex items-center justify-center text-sm mr-3 shadow-sm">2</span>
                Chi tiết lộ trình Rút & Cất Hàng
            </h3>
            <p class="text-sm text-slate-500 mt-2 ml-10">Bạn có thể điều chỉnh toàn bộ lộ trình bên dưới. Việc Chốt Kho sẽ tự động lưu lại các thay đổi.</p>
        </div>
        
        <!-- FORM LƯU HÀNG LOẠT (BULK UPDATE) -->
        <form id="bulk-update-form" action="{{ route('transfers.items.updateBulk', $transfer->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-[20%]">Sản phẩm</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-rose-500 uppercase tracking-wider w-[30%]">Nơi Rút Hàng (Kho Nguồn)</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-indigo-500 uppercase tracking-wider w-[10%]">SL Chở</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-emerald-500 uppercase tracking-wider w-[25%]">Nơi Cất Hàng (Kho Đích)</th>
                            @if($transfer->status === 'pending') 
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider w-[15%]">Thao tác</th> 
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($transfer->items as $item)
                            @php 
                                $inv = $item->inventory;
                                $batch = $inv->batch ?? null; 
                            @endphp
                            
                            <tr id="item-{{ $item->id }}" class="hover:bg-slate-50/50 transition-colors align-top">
                                <!-- SẢN PHẨM & NÚT TÁCH DÒNG -->
                                <td class="px-6 py-5 border-r border-slate-50">
                                    <p class="text-sm font-extrabold text-slate-900">{{ $item->product->name ?? 'N/A' }}</p>
                                    <p class="text-[11px] text-slate-500 mt-1 font-mono bg-slate-100 px-1.5 py-0.5 inline-block rounded border border-slate-200 mb-3">Mã: SP-{{ $item->product_id }}</p>
                                    
                                    @if($transfer->status === 'pending')
                                        <button type="submit" form="add-split-{{$item->id}}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded transition w-full flex items-center justify-center gap-1 border border-indigo-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Thêm Kệ Rút
                                        </button>
                                    @endif
                                </td>

                                <!-- NƠI RÚT HÀNG -->
                                <td class="px-6 py-5">
                                    @if($transfer->status === 'pending')
                                        <select name="items[{{ $item->id }}][inventory_id]" required class="select2 block w-full">
                                            <option value="">-- Chọn kệ rút hàng --</option>
                                            @if(isset($availableInventories[$item->product_id]))
                                                @foreach($availableInventories[$item->product_id] as $availInv)
                                                    @php
                                                        $b = $availInv->batch;
                                                        $hsd = $b && $b->expiry_date ? \Carbon\Carbon::parse($b->expiry_date)->format('d/m/y') : 'Ko HSD';
                                                        $lo = $b ? "Lô: {$b->batch_code}" : "Ko Lô";
                                                    @endphp
                                                    <option value="{{ $availInv->id }}" {{ $item->inventory_id == $availInv->id ? 'selected' : '' }}>
                                                        Kệ: {{ $availInv->location->name ?? 'N/A' }} | Tồn: {{ $availInv->quantity }} | {{ $lo }} ({{ $hsd }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @else
                                        <!-- Hiển thị khi đã chốt -->
                                        <div class="bg-rose-50/50 border border-rose-100 p-3 rounded-lg">
                                            <span class="text-xs font-bold text-rose-700 block mb-1">Kệ: {{ $inv->location->name ?? 'N/A' }}</span>
                                            @if($batch)
                                                <div class="mt-2 text-xs">
                                                    <span class="font-bold text-slate-600">Lô:</span> <span class="font-mono font-bold text-indigo-700">{{ $batch->batch_code }}</span><br>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                
                                <!-- SỐ LƯỢNG -->
                                @if($transfer->status === 'pending')
                                    <td class="px-6 py-5 align-top">
                                        <input type="number" name="items[{{ $item->id }}][quantity]" value="{{ $item->quantity }}" required min="1" class="w-full max-w-[100px] border-slate-300 rounded-lg text-center text-sm shadow-inner focus:ring-indigo-500 focus:border-indigo-500 font-black text-indigo-700 h-[42px] mx-auto block">
                                    </td>
                                    
                                    <!-- NƠI CẤT HÀNG -->
                                    <td class="px-6 py-5">
                                        <select name="items[{{ $item->id }}][to_location_id]" required class="select2 block w-full">
                                            <option value="">-- Chọn kệ cất --</option>
                                            @foreach($toLocations as $loc)
                                                <option value="{{ $loc->id }}" {{ $item->to_location_id == $loc->id ? 'selected' : '' }}>
                                                    {{ $loc->name }} ({{ strtoupper($loc->type) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    
                                    <!-- THAO TÁC XÓA ĐƠN LẺ -->
                                    <td class="px-6 py-5 text-center align-top border-l border-slate-50">
                                        <button type="submit" form="del-{{$item->id}}" class="w-full text-rose-600 hover:text-white border border-rose-300 hover:bg-rose-600 font-bold text-xs px-2 py-2.5 rounded transition shadow-sm bg-rose-50 mt-1" onclick="return confirm('Xóa lộ trình này khỏi phiếu?');">❌ XÓA DÒNG</button>
                                    </td>
                                @else
                                    <td class="px-6 py-5 text-center align-middle">
                                        <div class="text-xl font-black text-indigo-700">{{ $item->quantity }}</div>
                                    </td>
                                    <td class="px-6 py-5 align-middle" colspan="2">
                                        <div class="inline-flex items-center text-sm text-emerald-700 font-bold bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200 shadow-sm">
                                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Đã cất vào Kệ: {{ $item->toLocation->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="{{ $transfer->status === 'pending' ? '5' : '4' }}" class="px-6 py-12 text-center text-slate-500 font-medium bg-slate-50">Phiếu đang trống. Hãy thêm hàng hóa ở Bước 1.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- NÚT HÀNH ĐỘNG HÀNG LOẠT -->
            @if($transfer->status === 'pending' && count($transfer->items ?? []) > 0)
                <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-end items-center gap-4">
                    <button type="submit" form="cancel-form" class="w-full sm:w-auto bg-white border-2 border-rose-200 text-rose-600 px-6 py-3 rounded-lg font-bold shadow-sm hover:bg-rose-50 transition text-sm mr-auto" onclick="return confirm('Bạn chắc chắn muốn hủy bỏ toàn bộ phiếu điều chuyển này?');">❌ HỦY PHIẾU</button>
                    
                    <!-- Vẫn giữ nút Lưu Tạm Thời cho ai muốn bấm -->
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 shadow-md transition focus:ring-4 focus:ring-blue-200 flex items-center justify-center">
                        💾 LƯU TẠM THỜI
                    </button>

                    <!-- Nút Chốt mở cảnh báo nhắc nhở và kích hoạt AJAX ngầm -->
                    <button type="button" id="btn-submit-transfer" class="w-full sm:w-auto bg-emerald-600 text-white px-10 py-3 rounded-lg font-extrabold hover:bg-emerald-700 shadow-md transition focus:ring-4 focus:ring-emerald-200 flex items-center justify-center text-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        CHỐT KHO
                    </button>
                </div>
            @endif
        </form>

        <!-- ================= CÁC FORM NGẦM ĐỂ XỬ LÝ DỮ LIỆU ĐỘC LẬP ================= -->
        <form id="complete-form" action="{{ route('transfers.complete', $transfer->id) }}" method="POST" class="hidden">@csrf</form>
        <form id="cancel-form" action="{{ route('transfers.cancel', $transfer->id) }}" method="POST" class="hidden">@csrf</form>
        
        @if($transfer->status === 'pending')
            @foreach($transfer->items as $item)
                <!-- Form Tách dòng độc lập để tránh submit nhầm nguyên bảng -->
                <form id="add-split-{{$item->id}}" action="{{ route('transfers.items.add', $transfer->id) }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="quantity" value="1">
                </form>

                <form id="del-{{$item->id}}" action="{{ route('transfers.items.remove', [$transfer->id, $item->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
            @endforeach
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        language: { noResults: function() { return "Không tìm thấy kết quả"; } }
    });

    // XỬ LÝ NÚT CHỐT KHO (Tự động Lưu ngầm rồi mới Chốt)
    $('#btn-submit-transfer').click(function(e) {
        e.preventDefault();
        
        let hasEmptyDest = false;
        let hasEmptySource = false;
        
        $('select[name$="[to_location_id]"]').each(function() { if($(this).val() === '') hasEmptyDest = true; });
        $('select[name$="[inventory_id]"]').each(function() { if($(this).val() === '') hasEmptySource = true; });

        if (hasEmptySource) { alert('Vui lòng chọn Kệ rút hàng (Nguồn) cho tất cả các dòng.'); return; }
        if (hasEmptyDest) { alert('Vui lòng chọn Kệ cất hàng (Đích) cho tất cả các dòng.'); return; }

        if (confirm('Hành động này sẽ TỰ ĐỘNG CẬP NHẬT thông tin lộ trình và TRỪ TỒN KHO THỰC TẾ.\n\nBạn có chắc chắn chốt phiếu?')) {
            let $btn = $(this);
            let originalText = $btn.html();
            
            // 1. Hiển thị UI Đang xử lý
            $btn.html('<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> ĐANG XỬ LÝ...').prop('disabled', true);

            // 2. Tự động gửi form Lưu Tạm Thời qua AJAX
            $.ajax({
                url: $('#bulk-update-form').attr('action'),
                type: 'POST', // Form serialize đã có method PUT bên trong
                data: $('#bulk-update-form').serialize(),
                success: function(response) {
                    if (typeof response === 'string') {
                        // FIX TẠI ĐÂY: Dùng DOMParser thay vì includes() để tránh lỗi trùng lặp text trong script
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(response, "text/html");
                        
                        // Kiểm tra nếu có thẻ DIV báo lỗi màu đỏ (border-red-500)
                        if (doc.querySelector('.border-red-500')) {
                            // CÓ LỖI: Ghi đè màn hình hiện tại để hiện lỗi cho User xem
                            document.open();
                            document.write(response);
                            document.close();
                        } else {
                            // LƯU THÀNH CÔNG: Tự động chạy lệnh Chốt Kho
                            $('#complete-form').submit();
                        }
                    } else {
                        $('#complete-form').submit();
                    }
                },
                error: function(xhr) {
                    // Phục hồi lại nút nếu bị lỗi mạng hoặc lỗi validate dạng JSON 422
                    $btn.html(originalText).prop('disabled', false);
                    if (xhr.status === 422) {
                        alert('Dữ liệu không hợp lệ. Vui lòng kiểm tra lại số lượng hoặc các kệ hàng đã chọn.');
                    } else {
                        alert('Đã xảy ra lỗi kết nối. Vui lòng thử lại!');
                    }
                }
            });
        }
    });
});
</script>
@endsection