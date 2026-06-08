@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 pb-10">
        <div class="mb-4">
            <a href="{{ route('stock_takes.index') }}"
                class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Quay lại danh sách
            </a>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                    <div class="p-2 bg-indigo-50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    Khởi tạo Phiếu Kiểm Kê
                </h2>
                <span
                    class="px-3 py-1 bg-slate-100 text-slate-600 rounded-md text-sm font-semibold border border-slate-200 shadow-sm">Bước
                    1 / 2</span>
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

            <form action="{{ route('stock_takes.store') }}" method="POST">
                @csrf

                <div class="space-y-6 mb-8">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-inner">
                        <label class="block text-sm font-bold text-slate-800 mb-2">Nhà Kho cần kiểm kê <span
                                class="text-red-500">*</span></label>
                        <select name="warehouse_id" required
                            class="block w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 transition-colors sm:text-sm font-semibold cursor-pointer text-slate-900 shadow-sm">
                            <option value="">-- Vui lòng chọn Nhà Kho --</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-2 font-medium">Hệ thống sẽ tự động chốt (snapshot) tồn kho hiện
                            tại của nhà kho này khi bạn bắt đầu đếm.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Ghi chú kế hoạch (Tùy chọn)</label>
                        <textarea name="notes" rows="3"
                            class="block w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-indigo-500 sm:text-sm bg-white shadow-sm transition-colors"
                            placeholder="Ví dụ: Kế hoạch kiểm kê định kỳ cuối tháng..."></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-4 border-t border-slate-100 pt-6">
                    <a href="{{ route('stock_takes.index') }}"
                        class="px-6 py-2.5 border border-slate-300 rounded-lg text-slate-700 font-bold hover:bg-slate-50 transition shadow-sm">Hủy
                        bỏ</a>
                    <button type="submit"
                        class="px-8 py-2.5 bg-indigo-600 text-white rounded-lg font-bold shadow-md hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-200 flex items-center">
                        Tạo Phiếu & Chuyển sang Đếm
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
