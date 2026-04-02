@extends('layouts.admin')

@php
    $isEdit = isset($productAlert);
    $action = $isEdit ? route('product_alerts.update', $productAlert->id) : route('product_alerts.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            border-radius: 0.5rem;
            padding: 0.65rem 0.75rem;
            border-color: #cbd5e1;
            min-height: 44px;
            font-weight: 600;
        }

        .ts-control.focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
            border-color: #4f46e5;
        }
    </style>

    <div class="max-w-3xl mx-auto space-y-6 pb-12">
        <div class="mb-2">
            <a href="{{ route('product_alerts.index') }}"
                class="inline-flex items-center text-slate-500 hover:text-indigo-600 font-bold transition text-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                QUAY LẠI TRUNG TÂM CẢNH BÁO
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

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50">
                <h2 class="text-xl font-extrabold text-slate-800">
                    {{ $isEdit ? 'Sửa Cấu Hình Cảnh Báo' : 'Thêm Cấu Hình Cảnh Báo Mới' }}</h2>
            </div>

            <form action="{{ $action }}" method="POST" class="p-6 space-y-6">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <!-- Chọn sản phẩm -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Sản phẩm áp dụng <span
                            class="text-rose-500">*</span></label>
                    @if ($isEdit)
                        <input type="text"
                            value="{{ $productAlert->product->name }} (Mã: SP-{{ $productAlert->product_id }})" disabled
                            class="block w-full px-4 py-3 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 font-bold cursor-not-allowed">
                    @else
                        <select name="product_id" id="product_id" required class="block w-full">
                            <option value="">-- Gõ để tìm tên hoặc mã sản phẩm --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Mã: SP-{{ $product->id }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @if (!$isEdit)
                        <p class="text-xs text-slate-500 mt-2">* Chỉ hiển thị những sản phẩm chưa được cài đặt cảnh báo.</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ngưỡng Tồn Kho -->
                    <div class="bg-amber-50 p-5 rounded-xl border border-amber-200">
                        <label class="block text-sm font-bold text-amber-800 mb-2">Ngưỡng Báo Hết Hàng (Số Lượng) <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="stock_threshold"
                                value="{{ old('stock_threshold', $productAlert->stock_threshold ?? 10) }}" required
                                min="0"
                                class="block w-full px-4 py-3 border border-amber-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 font-black text-amber-700 text-lg">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-amber-500 font-bold">Sản phẩm</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-amber-600 mt-2 font-medium">Hệ thống sẽ báo động đỏ khi tổng tồn kho chạm
                            hoặc thấp hơn mức này.</p>
                    </div>

                    <!-- Ngưỡng Hạn Sử Dụng -->
                    <div class="bg-rose-50 p-5 rounded-xl border border-rose-200">
                        <label class="block text-sm font-bold text-rose-800 mb-2">Ngưỡng Báo Cận Date (Ngày) <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="expiry_threshold_days"
                                value="{{ old('expiry_threshold_days', $productAlert->expiry_threshold_days ?? 90) }}"
                                required min="0"
                                class="block w-full px-4 py-3 border border-rose-300 rounded-lg focus:ring-rose-500 focus:border-rose-500 font-black text-rose-700 text-lg">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-rose-500 font-bold">Ngày</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-rose-600 mt-2 font-medium">Báo động khi lô hàng còn x ngày nữa là hết hạn
                            (VD: 90 ngày = 3 tháng).</p>
                    </div>
                </div>

                <!-- Toggle Kích hoạt -->
                <div class="flex items-center bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                        {{ old('is_active', $productAlert->is_active ?? true) ? 'checked' : '' }}
                        class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                    <label for="is_active" class="ml-3 block text-sm font-bold text-slate-800 cursor-pointer">
                        Bật Cảnh Báo <br>
                        <span class="font-normal text-xs text-slate-500">Bỏ tick nếu bạn muốn tạm thời tắt thông báo cho sản
                            phẩm này.</span>
                    </label>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-200">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 transition shadow-md">
                        {{ $isEdit ? 'LƯU THAY ĐỔI' : 'TẠO CẤU HÌNH' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (!$isEdit)
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect("#product_id", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            });
        </script>
    @endif
@endsection
