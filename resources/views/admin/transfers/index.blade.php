@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center">
            <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            Quản lý Luân Chuyển Kho
        </h2>
        <a href="{{ route('transfers.create') }}" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-md hover:bg-indigo-700 transition flex items-center whitespace-nowrap text-sm">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tạo Phiếu Chuyển Kho
        </a>
    </div>

    <!-- BỘ LỌC TÌM KIẾM -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('transfers.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tìm Mã Phiếu</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-mono">TRF-</span>
                    <input type="text" name="search" value="{{ str_replace('TRF-', '', request('search')) }}" placeholder="00001" class="block w-full pl-14 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 sm:text-sm font-bold text-indigo-900 shadow-sm">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Trạng thái</label>
                <select name="status" onchange="this.form.submit()" class="block w-full py-2 px-3 border border-slate-300 rounded-lg shadow-sm sm:text-sm font-bold text-slate-700">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Đang xử lý</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>✔ Đã hoàn tất</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>❌ Đã hủy</option>
                </select>
            </div>
            <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition shadow-sm h-[38px]">Tìm kiếm</button>
        </form>
    </div>

    <!-- BẢNG DANH SÁCH -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Mã Phiếu</th>
                        <th class="px-6 py-4 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Lộ Trình</th>
                        <th class="px-6 py-4 text-left text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Nhân viên lập</th>
                        <th class="px-6 py-4 text-center text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-4 text-right text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($transfers as $transfer)
                        <tr class="hover:bg-slate-50 transition-colors align-middle">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-indigo-700">
                                TRF-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}
                                <p class="text-[10px] text-slate-400 font-medium mt-1">{{ $transfer->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</p>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-rose-50 border border-rose-100 px-2 py-1 rounded text-center flex-1">
                                        <span class="block text-[9px] font-black text-rose-500 uppercase">Kho Xuất</span>
                                        <span class="block text-sm font-bold text-slate-800 mt-0.5 truncate max-w-[120px]">{{ $transfer->fromWarehouse->name ?? 'N/A' }}</span>
                                    </div>
                                    <svg class="w-5 h-5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    <div class="bg-emerald-50 border border-emerald-100 px-2 py-1 rounded text-center flex-1">
                                        <span class="block text-[9px] font-black text-emerald-600 uppercase">Kho Nhận</span>
                                        <span class="block text-sm font-bold text-slate-800 mt-0.5 truncate max-w-[120px]">{{ $transfer->toWarehouse->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-700 font-semibold">{{ $transfer->staff->full_name ?? 'N/A' }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($transfer->status === 'completed')
                                    <span class="inline-flex px-3 py-1.5 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Đã luân chuyển</span>
                                @elseif($transfer->status === 'cancelled')
                                    <span class="inline-flex px-3 py-1.5 rounded-md text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">Đã hủy</span>
                                @else
                                    <span class="inline-flex px-3 py-1.5 rounded-md text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">⏳ Chờ xử lý</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('transfers.show', $transfer->id) }}" class="inline-flex text-indigo-600 hover:text-white bg-indigo-50 hover:bg-indigo-600 border border-indigo-200 px-4 py-2 rounded-lg font-bold text-sm transition shadow-sm">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-16 text-center text-slate-500 font-medium">Chưa có phiếu luân chuyển nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transfers->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection