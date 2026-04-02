@extends('layouts.admin')

@section('content')
    <x-crud.index title="Quản lý Tồn kho" createRoute="{{ route('inventory.create') }}" :data="$inventories">
        <!-- FILTER FORM NHƯ BƯỚC TRƯỚC -->
        <div
            class="mb-6 bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-sm font-semibold text-slate-800">Bộ lọc & Tìm kiếm</h3>
            </div>

            <div class="p-6">
                <form action="{{ route('inventory.index') }}" method="GET">
                    @if (request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if (request('dir'))
                        <input type="hidden" name="dir" value="{{ request('dir') }}">
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-5 items-end">

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tìm kiếm sản phẩm</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="keyword" value="{{ request('keyword') }}"
                                    placeholder="Nhập tên, mã SP..."
                                    class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors duration-200">
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nhà kho</label>
                            <select name="warehouse_id"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-700 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors duration-200 hover:bg-slate-100 cursor-pointer">
                                <option value="">-- Tất cả kho --</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}"
                                        {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Mã Lô (Batch)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <input type="text" name="batch_code" value="{{ request('batch_code') }}"
                                    placeholder="Nhập mã lô..."
                                    class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors duration-200">
                            </div>
                        </div>

                        <div class="md:col-span-3 flex items-center justify-end gap-2">
                            @if (request()->anyFilled(['keyword', 'warehouse_id', 'stock_status', 'batch_code']))
                                <a href="{{ route('inventory.index') }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-slate-600 text-sm font-medium rounded-lg border border-slate-300 hover:bg-slate-50 hover:text-red-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-200 w-full lg:w-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Xóa lọc
                                </a>
                            @endif
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm hover:shadow focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-all duration-200 w-full lg:w-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Lọc
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- BẢNG DỮ LIỆU (Có Sorting) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">

                            <!-- CỘT ID (CÓ SORT) -->
                            <th class="px-6 py-4 font-bold">
                                @php
                                    $sortIdDir = request('sort') == 'id' && request('dir') == 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'dir' => $sortIdDir]) }}"
                                    class="flex items-center group hover:text-indigo-600 transition">
                                    ID
                                    <span
                                        class="ml-1 text-slate-300 group-hover:text-indigo-600 {{ request('sort') == 'id' ? 'text-indigo-600' : '' }}">
                                        @if (request('sort') == 'id' && request('dir') == 'desc')
                                            &darr;
                                        @else
                                            &uarr;
                                        @endif
                                    </span>
                                </a>
                            </th>

                            <!-- CỘT SẢN PHẨM (Không Sort trực tiếp vì là relation) -->
                            <th class="px-6 py-4 font-bold">Sản phẩm</th>

                            <th class="px-6 py-4 font-bold">Lô & Hạn sử dụng</th>
                            <th class="px-6 py-4 font-bold">Vị trí</th>

                            <!-- CỘT SỐ LƯỢNG (CÓ SORT) -->
                            <th class="px-6 py-4 font-bold text-center">
                                @php
                                    $sortQtyDir =
                                        request('sort') == 'quantity' && request('dir') == 'desc' ? 'asc' : 'desc';
                                @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'quantity', 'dir' => $sortQtyDir]) }}"
                                    class="inline-flex items-center justify-center group hover:text-indigo-600 transition">
                                    Tổng Tồn Kho
                                    <span
                                        class="ml-1 text-slate-300 group-hover:text-indigo-600 {{ request('sort') == 'quantity' ? 'text-indigo-600' : '' }}">
                                        @if (request('sort') == 'quantity' && request('dir') == 'asc')
                                            &uarr;
                                        @else
                                            &darr;
                                        @endif
                                    </span>
                                </a>
                            </th>

                            <th class="px-6 py-4 font-bold text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($inventories as $inv)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-bold">
                                    #{{ $inv->id }}</td>

                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <div class="font-extrabold text-indigo-700">{{ $inv->product->name ?? 'N/A' }}</div>
                                    <div
                                        class="text-xs text-slate-500 font-mono mt-1 bg-slate-100 inline-block px-2 py-0.5 rounded border border-slate-200">
                                        SP-{{ $inv->product_id }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($inv->batch)
                                        <div class="font-bold text-slate-800">{{ $inv->batch->batch_code }}</div>
                                        <div
                                            class="text-xs mt-1 font-semibold {{ $inv->batch->expiry_date && \Carbon\Carbon::parse($inv->batch->expiry_date)->isPast() ? 'text-rose-500' : 'text-slate-500' }}">
                                            HSD:
                                            {{ $inv->batch->expiry_date ? \Carbon\Carbon::parse($inv->batch->expiry_date)->format('d/m/Y') : 'N/A' }}
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Không chia lô</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-bold text-slate-800">{{ $inv->location->warehouse->name ?? 'N/A' }}
                                    </div>
                                    <div
                                        class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded mt-1 inline-block border border-emerald-200">
                                        {{ $inv->location->name ?? 'N/A' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div
                                        class="font-black text-xl {{ $inv->quantity > 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                                        {{ number_format($inv->quantity) }}
                                    </div>
                                    @if ($inv->reserved_quantity > 0)
                                        <div class="text-[10px] font-bold text-amber-600 mt-1" title="Đang bị giữ">
                                            Giữ chỗ: {{ number_format($inv->reserved_quantity) }}
                                        </div>
                                        <div class="text-[10px] font-bold text-emerald-600">
                                            Khả dụng: {{ number_format($inv->quantity - $inv->reserved_quantity) }}
                                        </div>
                                    @else
                                        <div class="text-[10px] font-bold text-emerald-600 mt-1">Khả dụng:
                                            {{ number_format($inv->quantity) }}</div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('inventory.show', $inv->id) }}"
                                        class="text-blue-600 hover:text-blue-800 font-bold mr-3">Chi tiết</a>
                                    <a href="{{ route('inventory.edit', $inv->id) }}"
                                        class="text-indigo-600 hover:text-indigo-800 font-bold">Chỉnh sửa</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 font-medium bg-slate-50">
                                    Không tìm thấy dữ liệu tồn kho.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PHÂN TRANG: Nhớ giữ lại parameter -->
        <div class="mt-4">
            {{ $inventories->appends(request()->query())->links() }}
        </div>
    </x-crud.index>
@endsection
