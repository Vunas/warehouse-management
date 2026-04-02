 @extends('layouts.admin')

 @section('content')
     <div class="max-w-[90rem] mx-auto space-y-8 pb-12">
         <!-- Header -->
         <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
             <div>
                 <h2 class="text-2xl font-extrabold text-slate-800">🚨 Trung Tâm Cảnh Báo</h2>
                 <p class="text-sm text-slate-500 mt-1">Giám sát tự động lượng hàng sắp cạn và các lô hàng cận date.</p>
             </div>
             <a href="{{ route('product_alerts.create') }}"
                 class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm flex items-center text-sm">
                 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                 </svg>
                 Thêm Cấu Hình Ngưỡng
             </a>
         </div>

         @if (session('success'))
             <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md shadow-sm font-bold text-emerald-800">
                 {{ session('success') }}</div>
         @endif

         <!-- PHẦN 1: BẢNG TIN CẢNH BÁO (REAL-TIME) -->
         <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

             <!-- Cảnh báo Tồn Kho -->
             <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                 <div class="bg-amber-50 border-b border-amber-100 p-5 flex items-center justify-between">
                     <h3 class="font-black text-amber-800 flex items-center">
                         <svg class="w-6 h-6 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                         </svg>
                         Sản Phẩm Sắp Hết Hàng
                         <span
                             class="ml-3 bg-amber-500 text-white px-2 py-0.5 rounded-full text-xs">{{ count($triggeredAlerts['low_stock']) }}</span>
                     </h3>
                 </div>
                 <div class="p-0 overflow-y-auto max-h-96 flex-1">
                     @forelse($triggeredAlerts['low_stock'] as $alert)
                         <div
                             class="flex items-center justify-between p-4 border-b border-slate-100 hover:bg-slate-50 transition">
                             <div>
                                 <p class="font-bold text-slate-800 text-sm">{{ $alert->product->name }}</p>
                                 <p class="text-[11px] text-slate-500 font-mono mt-0.5">Mã: SP-{{ $alert->product->id }}</p>
                             </div>
                             <div class="text-right flex items-center gap-3">
                                 <div class="text-xs">
                                     <span class="text-slate-500">Đang có:</span>
                                     <span
                                         class="font-black text-lg {{ $alert->is_out_of_stock ? 'text-rose-600' : 'text-amber-600' }}">{{ $alert->current_stock }}</span>
                                     <span class="text-slate-400">/ Ngưỡng: {{ $alert->threshold }}</span>
                                 </div>
                                 @if ($alert->is_out_of_stock)
                                     <span
                                         class="bg-rose-100 text-rose-700 px-2 py-1 rounded text-[10px] font-black uppercase border border-rose-200">Đã
                                         Hết</span>
                                 @else
                                     <span
                                         class="bg-amber-100 text-amber-700 px-2 py-1 rounded text-[10px] font-black uppercase border border-amber-200">Sắp
                                         Hết</span>
                                 @endif
                             </div>
                         </div>
                     @empty
                         <div class="p-8 text-center text-slate-500 flex flex-col items-center">
                             <svg class="w-12 h-12 text-emerald-300 mb-2" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                     d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                             </svg>
                             <p class="font-bold">Tuyệt vời! Không có sản phẩm nào thiếu hụt.</p>
                         </div>
                     @endforelse
                 </div>
             </div>

             <!-- Cảnh báo Hạn Sử Dụng -->
             <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                 <div class="bg-rose-50 border-b border-rose-100 p-5 flex items-center justify-between">
                     <h3 class="font-black text-rose-800 flex items-center">
                         <svg class="w-6 h-6 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                         </svg>
                         Lô Hàng Cận Date / Hết Hạn
                         <span
                             class="ml-3 bg-rose-500 text-white px-2 py-0.5 rounded-full text-xs">{{ count($triggeredAlerts['expiring_soon']) }}</span>
                     </h3>
                 </div>
                 <div class="p-0 overflow-y-auto max-h-96 flex-1">
                     @forelse($triggeredAlerts['expiring_soon'] as $alert)
                         <div
                             class="flex items-center justify-between p-4 border-b border-slate-100 hover:bg-slate-50 transition">
                             <div>
                                 <p class="font-bold text-slate-800 text-sm">{{ $alert->product->name }}</p>
                                 <p class="text-[11px] text-slate-500 font-mono mt-0.5">Lô: <span
                                         class="font-bold text-slate-700">{{ $alert->batch->batch_code }}</span> |
                                     SP-{{ $alert->product->id }}</p>
                             </div>
                             <div class="text-right">
                                 <p class="text-xs text-slate-500 font-bold">HSD:
                                     {{ \Carbon\Carbon::parse($alert->batch->expiry_date)->format('d/m/Y') }}</p>
                                 @if ($alert->days_left < 0)
                                     <span
                                         class="bg-rose-600 text-white px-2 py-1 rounded text-[10px] font-black uppercase mt-1 inline-block shadow-sm">
                                         Quá hạn {{ abs($alert->days_left) }} ngày
                                     </span>
                                 @elseif($alert->days_left == 0)
                                     <span
                                         class="bg-rose-500 text-white px-2 py-1 rounded text-[10px] font-black uppercase mt-1 inline-block shadow-sm">
                                         Hết hạn hôm nay!
                                     </span>
                                 @else
                                     <span
                                         class="bg-amber-100 text-amber-700 border border-amber-200 px-2 py-1 rounded text-[10px] font-black uppercase mt-1 inline-block">
                                         Còn {{ $alert->days_left }} ngày
                                     </span>
                                 @endif
                             </div>
                         </div>
                     @empty
                         <div class="p-8 text-center text-slate-500 flex flex-col items-center">
                             <svg class="w-12 h-12 text-emerald-300 mb-2" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                     d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                             </svg>
                             <p class="font-bold">An toàn! Không có lô hàng nào cận date.</p>
                         </div>
                     @endforelse
                 </div>
             </div>
         </div>

         <!-- PHẦN 2: DANH SÁCH CẤU HÌNH -->
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-8">
             <div class="p-5 border-b border-slate-200 bg-slate-50">
                 <h3 class="text-lg font-extrabold text-slate-800">⚙️ Cấu Hình Ngưỡng Cảnh Báo</h3>
             </div>

             <div class="overflow-x-auto">
                 <table class="min-w-full divide-y divide-slate-200">
                     <thead class="bg-slate-100">
                         <tr>
                             <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Sản
                                 phẩm</th>
                             <th
                                 class="px-6 py-4 text-center text-xs font-bold text-amber-600 uppercase tracking-wider bg-amber-50">
                                 Ngưỡng Tồn (SL)</th>
                             <th
                                 class="px-6 py-4 text-center text-xs font-bold text-rose-500 uppercase tracking-wider bg-rose-50">
                                 Ngưỡng HSD (Ngày)</th>
                             <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                 Trạng Thái</th>
                             <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Thao
                                 tác</th>
                         </tr>
                     </thead>
                     <tbody class="bg-white divide-y divide-slate-100">
                         @forelse($alerts as $al)
                             <tr class="hover:bg-slate-50 transition-colors {{ !$al->is_active ? 'opacity-60' : '' }}">
                                 <td class="px-6 py-4">
                                     <p class="text-sm font-bold text-slate-800">{{ $al->product->name }}</p>
                                     <p class="text-[11px] text-slate-500 font-mono mt-0.5">Mã: SP-{{ $al->product_id }}
                                     </p>
                                 </td>
                                 <td class="px-6 py-4 text-center text-lg font-black text-amber-600 bg-amber-50/30">
                                     <= {{ $al->stock_threshold }} </td>
                                 <td class="px-6 py-4 text-center text-lg font-black text-rose-500 bg-rose-50/30">
                                     <= {{ $al->expiry_threshold_days }} </td>
                                 <td class="px-6 py-4 text-center">
                                     <form action="{{ route('product_alerts.toggle', $al->id) }}" method="POST">
                                         @csrf @method('PATCH')
                                         <button type="submit"
                                             class="px-3 py-1 rounded-full text-xs font-bold border transition-colors {{ $al->is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                                             {{ $al->is_active ? 'ĐANG BẬT' : 'ĐÃ TẮT' }}
                                         </button>
                                     </form>
                                 </td>
                                 <td class="px-6 py-4 text-right">
                                     <a href="{{ route('product_alerts.edit', $al->id) }}"
                                         class="text-indigo-600 hover:text-indigo-900 font-bold text-sm mr-3">Sửa</a>
                                     <form action="{{ route('product_alerts.destroy', $al->id) }}" method="POST"
                                         class="inline"
                                         onsubmit="return confirm('Bạn có chắc chắn muốn xóa cấu hình này?');">
                                         @csrf @method('DELETE')
                                         <button type="submit"
                                             class="text-rose-600 hover:text-rose-900 font-bold text-sm">Xóa</button>
                                     </form>
                                 </td>
                             </tr>
                         @empty
                             <tr>
                                 <td colspan="5" class="px-6 py-12 text-center text-slate-500">Chưa thiết lập ngưỡng
                                     cảnh báo nào.</td>
                             </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
             <div class="p-4 border-t border-slate-100">{{ $alerts->links() }}</div>
         </div>
     </div>
 @endsection
