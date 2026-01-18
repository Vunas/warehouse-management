@extends('layouts.admin')

@section('title', 'Báo cáo Tồn kho')
@section('header', 'Tồn Kho Hiện Tại')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Filter Toolbar -->
    <div class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-lg">
        <form action="{{ route('inventory.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Kho hàng</label>
                <select name="warehouse_id" class="border rounded px-3 py-2 text-sm w-48 focus:ring-blue-500">
                    <option value="">-- Tất cả kho --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Tìm kiếm</label>
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên SP, SKU..." class="pl-8 pr-3 py-2 border rounded text-sm w-64 focus:outline-none focus:border-blue-500">
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Lọc
            </button>
            <a href="{{ route('inventory.index') }}" class="text-gray-500 text-sm hover:text-gray-700 underline ml-2">Reset</a>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-white text-gray-500 border-b uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Sản phẩm</th>
                    <th class="px-6 py-3">Vị trí (Lô/Kệ)</th>
                    <th class="px-6 py-3 text-center">Số lượng</th>
                    <th class="px-6 py-3 text-center">Slot chiếm dụng</th>
                    <th class="px-6 py-3">Ngày nhập (FIFO)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500 mr-3">
                                <i class="fa-solid fa-box"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800">{{ $item->product->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $item->product->sku }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-blue-600 font-bold font-mono">{{ $item->storageBlock->block_code }}</div>
                        <div class="text-xs text-gray-500">{{ $item->storageBlock->warehouse->name }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded font-bold text-xs">
                            {{ number_format($item->current_quantity) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{ $item->slot_used }} slots
                        @if($item->calc_id)
                            <div class="text-[10px] text-gray-400 mt-1">Rule: #{{ $item->calc_id }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs">
                        <div>{{ $item->imported_at->format('d/m/Y') }}</div>
                        <div class="text-gray-400">{{ $item->imported_at->diffForHumans() }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        <i class="fa-solid fa-clipboard-list text-2xl mb-2"></i><br>
                        Không tìm thấy dữ liệu tồn kho.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-100">
        {{ $items->withQueryString()->links() }}
    </div>
</div>
@endsection