@extends('layouts.admin')

@section('content')
<div class="max-w-[90rem] mx-auto space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800">📖 Sổ Kho (Lịch sử Giao dịch)</h2>
            <p class="text-sm text-slate-500 mt-1">Ghi nhận mọi biến động Tồn kho: Nhập, Xuất, Chuyển kệ, Kiểm kê điều chỉnh.</p>
        </div>
    </div>

    <!-- Bộ Lọc -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('inventory_transactions.index') }}" method="GET" class="flex flex-wrap md:flex-nowrap gap-4 items-end">
            <div class="w-full md:w-64">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Loại Biến Động</label>
                <select name="type" class="block w-full border-slate-300 rounded-lg text-sm focus:ring-indigo-500 h-[42px]">
                    <option value="">-- Tất cả --</option>
                    <option value="inbound" {{ request('type') == 'inbound' ? 'selected' : '' }}>🟢 Nhập Kho</option>
                    <option value="outbound" {{ request('type') == 'outbound' ? 'selected' : '' }}>🔴 Xuất Kho</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>🔵 Chuyển Kho</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>🟣 Kiểm kê / Điều chỉnh</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Tìm Sản phẩm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên sản phẩm hoặc ID (VD: SP-1)" class="block w-full border-slate-300 rounded-lg text-sm focus:ring-indigo-500 h-[42px]">
            </div>
            
            <button type="submit" class="bg-slate-800 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-slate-900 transition h-[42px]">
                Lọc Dữ Liệu
            </button>
            <a href="{{ route('inventory_transactions.index') }}" class="bg-slate-100 text-slate-600 px-4 py-2.5 rounded-lg font-bold hover:bg-slate-200 transition h-[42px] flex items-center">
                Xóa lọc
            </a>
        </form>
    </div>

    <!-- Bảng Dữ Liệu Sổ Kho -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Thời Gian</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Loại Giao Dịch</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Sản phẩm & Lô</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Vị Trí Cập Nhật</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">SL Thay Đổi</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-indigo-500 uppercase bg-indigo-50 border-x border-indigo-100">Tồn Cuối</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nhân sự & Ghi chú</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($transactions as $log)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <!-- Thời gian -->
                        <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                            <span class="block text-slate-800 font-bold text-sm">{{ $log->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</span>
                            {{ $log->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i:s') }}
                        </td>
                        
                        <!-- Loại giao dịch -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($log->transaction_type == 'inbound')
                                <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded text-xs font-bold border border-emerald-200">🟢 Nhập kho</span>
                            @elseif($log->transaction_type == 'outbound')
                                <span class="bg-rose-100 text-rose-800 px-2.5 py-1 rounded text-xs font-bold border border-rose-200">🔴 Xuất kho</span>
                            @elseif($log->transaction_type == 'transfer')
                                <span class="bg-blue-100 text-blue-800 px-2.5 py-1 rounded text-xs font-bold border border-blue-200">🔵 Chuyển kho</span>
                            @elseif($log->transaction_type == 'adjustment')
                                <span class="bg-purple-100 text-purple-800 px-2.5 py-1 rounded text-xs font-bold border border-purple-200">🟣 Kiểm kê</span>
                            @endif
                            <div class="text-[10px] text-slate-400 mt-1.5 font-mono">Tham chiếu: #{{ $log->reference_id ?? 'N/A' }}</div>
                        </td>
                        
                        <!-- Sản phẩm & Lô -->
                        <td class="px-5 py-4">
                            <p class="text-sm font-extrabold text-slate-800">{{ $log->product->name ?? 'N/A' }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Mã: SP-{{ $log->product_id }}</p>
                            @if($log->batch)
                                <span class="text-[10px] bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded font-mono text-slate-600 mt-1 inline-block">Lô: {{ $log->batch->batch_code }}</span>
                            @endif
                        </td>
                        
                        <!-- Vị trí -->
                        <td class="px-5 py-4 text-sm">
                            <p class="font-bold text-slate-700">{{ $log->location->warehouse->name ?? 'N/A' }}</p>
                            <p class="text-xs text-emerald-600 font-semibold">Kệ: {{ $log->location->name ?? 'N/A' }}</p>
                        </td>
                        
                        <!-- Số lượng thay đổi -->
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            @if($log->quantity_change > 0)
                                <span class="text-lg font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">+{{ $log->quantity_change }}</span>
                            @elseif($log->quantity_change < 0)
                                <span class="text-lg font-black text-rose-600 bg-rose-50 px-2 py-0.5 rounded">{{ $log->quantity_change }}</span>
                            @else
                                <span class="text-lg font-black text-slate-400">0</span>
                            @endif
                        </td>
                        
                        <!-- Số tồn cuối -->
                        <td class="px-5 py-4 whitespace-nowrap text-center bg-indigo-50/30 border-x border-indigo-50">
                            <span class="text-xl font-black text-indigo-700">{{ $log->balance_after }}</span>
                        </td>
                        
                        <!-- Nhân sự & Note -->
                        <td class="px-5 py-4 text-xs text-slate-600">
                            <div class="flex items-center gap-1.5 font-bold mb-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $log->staff->name ?? 'System' }}
                            </div>
                            <span class="italic text-slate-500 block max-w-xs break-words">"{{ $log->note ?? 'Không có ghi chú' }}"</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-slate-500 font-medium bg-slate-50">Chưa có giao dịch nào được ghi nhận.</td></tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4 border-t border-slate-100">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection