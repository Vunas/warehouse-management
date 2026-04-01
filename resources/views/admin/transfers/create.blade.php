@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('transfers.index') }}" class="inline-flex items-center text-slate-500 hover:text-indigo-600 font-bold transition text-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            QUAY LẠI DANH SÁCH
        </a>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <h2 class="text-2xl font-extrabold text-slate-800 mb-6 border-b border-slate-100 pb-4 flex items-center">
            <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </span>
            Tạo Phiếu Chuyển Kho (Giữa Các Nhà Kho)
        </h2>

        <div class="bg-indigo-50/50 border border-indigo-100 p-5 rounded-xl mb-8">
            <p class="text-sm text-indigo-800 font-medium leading-relaxed">
                <strong class="text-indigo-900 font-black">BƯỚC 1:</strong> Chỉ định Nhà Kho xuất hàng đi và Nhà Kho nhận hàng đến.<br>
                <strong class="text-indigo-900 font-black">BƯỚC 2:</strong> Ở màn hình tiếp theo, bạn chọn Sản Phẩm, hệ thống sẽ <strong>tự động quét và rút hàng</strong> từ các kệ ở Kho xuất (ưu tiên FEFO/FIFO).
            </p>
        </div>

        <form action="{{ route('transfers.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 relative">
                <!-- KHỐI TỪ NGUỒN (FROM) -->
                <div class="bg-rose-50/30 p-6 rounded-xl border border-rose-100 relative z-10">
                    <h3 class="text-sm font-black text-rose-700 mb-4 flex items-center uppercase tracking-wider">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Lấy hàng từ Kho nào?
                    </h3>
                    
                    <div>
                        <select name="from_warehouse_id" required class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-rose-500 py-3 font-bold text-slate-800 bg-white">
                            <option value="">-- Chọn Nhà Kho Xuất --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @error('from_warehouse_id') <p class="mt-2 text-xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- ICON MŨI TÊN GIỮA 2 CỘT -->
                <div class="hidden md:flex absolute inset-0 justify-center items-center pointer-events-none z-20">
                    <div class="bg-white p-3 rounded-full shadow-md border border-slate-100 text-indigo-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>

                <!-- KHỐI ĐẾN ĐÍCH (TO) -->
                <div class="bg-emerald-50/30 p-6 rounded-xl border border-emerald-100 relative z-10">
                    <h3 class="text-sm font-black text-emerald-700 mb-4 flex items-center uppercase tracking-wider">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Cất hàng vào Kho nào?
                    </h3>
                    
                    <div>
                        <select name="to_warehouse_id" required class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 py-3 font-bold text-slate-800 bg-white">
                            <option value="">-- Chọn Nhà Kho Nhận --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @error('to_warehouse_id') <p class="mt-2 text-xs text-rose-600 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-5 border-t border-slate-100">
                <button type="submit" class="bg-indigo-600 text-white px-8 py-3.5 rounded-lg font-extrabold shadow-md hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-200 flex items-center">
                    Khởi tạo phiếu chuyển <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection