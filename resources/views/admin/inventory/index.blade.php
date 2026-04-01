@extends('layouts.admin')

@section('content')
    <x-crud.index 
        title="Quản lý Tồn kho" 
        createRoute="{{ route('inventory.create') }}" 
        :data="$inventories"
    >
        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">ID</x-ui.table.column>
                <x-ui.table.column name="product">Sản phẩm</x-ui.table.column>
                <x-ui.table.column name="batch">Lô hàng (Batch)</x-ui.table.column>
                <x-ui.table.column name="location">Vị trí (Kho & Kệ)</x-ui.table.column>
                <x-ui.table.column name="quantity" align="center">Tồn kho</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($inventories as $inv)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-bold">#{{ $inv->id }}</td>
                    
                    <!-- Sản phẩm -->
                    <td class="px-6 py-4 text-sm text-slate-900 font-medium">
                        <div class="flex flex-col">
                            <span class="font-extrabold text-indigo-700 text-base">{{ $inv->product->name ?? 'N/A' }}</span>
                            <span class="text-xs text-slate-500 font-mono mt-1 bg-slate-100 inline-block w-max px-2 py-0.5 rounded border border-slate-200">Mã: SP-{{ $inv->product_id }}</span>
                        </div>
                    </td>
                    
                    <!-- Lô Hàng -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        @if($inv->batch)
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800">Lô: {{ $inv->batch->batch_code }}</span>
                                <span class="text-xs mt-1 font-bold {{ $inv->batch->expiry_date && \Carbon\Carbon::parse($inv->batch->expiry_date)->isPast() ? 'text-rose-500' : 'text-slate-500' }}">
                                    HSD: {{ $inv->batch->expiry_date ? \Carbon\Carbon::parse($inv->batch->expiry_date)->format('d/m/Y') : 'Không có' }}
                                    @if($inv->batch->expiry_date && \Carbon\Carbon::parse($inv->batch->expiry_date)->isPast())
                                        (Hết hạn)
                                    @endif
                                </span>
                            </div>
                        @else
                            <span class="text-xs font-semibold text-slate-400 italic bg-slate-50 px-2 py-1 rounded border border-dashed border-slate-200">Không chia lô</span>
                        @endif
                    </td>
                    
                    <!-- Vị trí -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800">{{ $inv->location->warehouse->name ?? 'N/A' }}</span>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded mt-1.5 inline-block w-max border border-emerald-200">
                                Kệ: {{ $inv->location->name ?? 'N/A' }}
                            </span>
                        </div>
                    </td>
                    
                    <!-- Số lượng -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        <div class="flex flex-col items-center">
                            <span class="font-black text-xl {{ $inv->quantity > 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                                {{ $inv->quantity }}
                            </span>
                            
                            @if($inv->reserved_quantity > 0)
                                <div class="mt-1 flex flex-col items-center gap-1">
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded shadow-sm" title="Đang bị giữ chỗ cho đơn hàng hoặc phiếu chuyển">
                                        Giữ: {{ $inv->reserved_quantity }}
                                    </span>
                                    <span class="text-[10px] font-bold text-emerald-600">
                                        Khả dụng: {{ $inv->quantity - $inv->reserved_quantity }}
                                    </span>
                                </div>
                            @else
                                <span class="text-[10px] font-bold text-emerald-600 mt-1">
                                    Khả dụng: {{ $inv->quantity }}
                                </span>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Thao tác -->
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('inventory.show', $inv->id) }}" class="text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-200 bg-blue-50 px-3 py-1.5 rounded-md transition-colors text-xs font-bold shadow-sm">Chi tiết</a>
                            <a href="{{ route('inventory.edit', $inv->id) }}" class="text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-200 bg-indigo-50 px-3 py-1.5 rounded-md transition-colors text-xs font-bold shadow-sm">Sửa SL</a>
                            
                            <form action="{{ route('inventory.destroy', $inv->id) }}" method="POST" class="inline" onsubmit="return confirm('Cảnh báo: Xóa trực tiếp tồn kho sẽ làm lệch dữ liệu hệ thống. Bạn có chắc chắn?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 bg-rose-50 px-3 py-1.5 rounded-md transition-colors text-xs font-bold shadow-sm">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 font-medium bg-slate-50">Kho đang trống.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection