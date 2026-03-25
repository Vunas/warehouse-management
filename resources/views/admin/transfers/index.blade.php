@extends('layouts.admin')

@section('content')
    <x-crud.index 
        title="Phiếu Luân chuyển kho" 
        createRoute="{{ route('transfers.create') }}" 
        :data="$transfers"
    >
        <!-- BỔ SUNG BỘ LỌC TÌM KIẾM -->
        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('transfers.index') }}">
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo Mã phiếu..." class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 sm:text-sm">
                </div>
                <select name="status" onchange="this.form.submit()" class="block w-full md:w-40 py-2 px-3 border border-gray-300 rounded-lg sm:text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Đã hoàn tất</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">Mã Phiếu</x-ui.table.column>
                <x-ui.table.column name="from">Từ Vị trí</x-ui.table.column>
                <x-ui.table.column name="to">Đến Vị trí</x-ui.table.column>
                <x-ui.table.column name="staff">Nhân viên tạo</x-ui.table.column>
                <x-ui.table.column name="status" align="center">Trạng thái</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($transfers as $transfer)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-700">#TRF-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $transfer->fromLocation->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $transfer->toLocation->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $transfer->staff->full_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($transfer->status === 'completed')
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Đã hoàn tất</span>
                        @elseif($transfer->status === 'cancelled')
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Đã hủy</span>
                        @else
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">Đang xử lý</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('transfers.show', $transfer->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md font-medium text-sm transition">
                            Chi tiết & Xử lý
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có phiếu luân chuyển nào.</td></tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection