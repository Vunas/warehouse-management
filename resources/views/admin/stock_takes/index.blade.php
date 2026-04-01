@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-extrabold text-slate-800">📋 Quản lý Kiểm Kê Kho</h2>
        
        <!-- Nút mở Modal Tạo Phiếu Mới (Sử dụng CSS thuần hoặc alpinejs tuỳ bạn) -->
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold shadow-md hover:bg-indigo-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tạo Phiếu Kiểm Kê
        </button>
    </div>

    <!-- Thông báo -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md shadow-sm font-bold text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Mã Phiếu</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nhà Kho</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Người tạo</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Ngày tạo</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($stockTakes as $st)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-indigo-600">{{ $st->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">{{ $st->warehouse->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $st->staff->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($st->status == 'draft') <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded text-xs font-bold border border-slate-200">Nháp</span>
                            @elseif($st->status == 'counting') <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded text-xs font-bold border border-amber-200">Đang đếm</span>
                            @elseif($st->status == 'completed') <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold border border-emerald-200">Đã chốt sổ</span>
                            @else <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded text-xs font-bold border border-rose-200">Đã Hủy</span> @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-500">{{ $st->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('stock_takes.show', $st->id) }}" class="text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-200 bg-indigo-50 px-4 py-2 rounded-md transition-colors text-xs font-bold shadow-sm">
                                Xem / Xử lý
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Chưa có phiếu kiểm kê nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">{{ $stockTakes->links() }}</div>
    </div>
</div>

<!-- Modal Tạo Mới -->
<div id="createModal" class="fixed inset-0 bg-slate-900 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <h3 class="text-xl font-extrabold text-slate-800 mb-4">Khởi tạo Phiếu Kiểm Kê</h3>
        <form action="{{ route('stock_takes.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Chọn Nhà Kho cần kiểm *</label>
                    <select name="warehouse_id" required class="block w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Chọn Nhà Kho --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Ghi chú (Tùy chọn)</label>
                    <textarea name="notes" rows="3" class="block w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Kế hoạch kiểm kê định kỳ tháng..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">Hủy</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition">Tạo Phiếu</button>
            </div>
        </form>
    </div>
</div>
@endsection