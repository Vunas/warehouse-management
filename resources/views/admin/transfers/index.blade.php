@extends('layouts.admin')

@section('title', 'Chuyển kho nội bộ')
@section('header', 'Lịch sử Chuyển Kho')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <span class="text-sm text-gray-500">Điều chuyển hàng hóa giữa các Lô hoặc Kho để tối ưu không gian.</span>
        @can('create', App\Models\InternalTransfer::class)
        <a href="{{ route('transfers.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 shadow-sm">
            <i class="fa-solid fa-arrow-right-arrow-left mr-1"></i> Tạo Lệnh Chuyển
        </a>
        @endcan
    </div>

    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3">Mã Lệnh</th>
                <th class="px-6 py-3">Từ (Nguồn)</th>
                <th class="px-6 py-3">Đến (Đích)</th>
                <th class="px-6 py-3">Lý do</th>
                <th class="px-6 py-3">Trạng thái</th>
                <th class="px-6 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transfers as $transfer)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-mono font-bold text-indigo-600">#TRF-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-800">{{ $transfer->fromBlock->block_code }}</div>
                    <div class="text-xs text-gray-500">{{ $transfer->fromBlock->warehouse->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-800">{{ $transfer->toBlock->block_code }}</div>
                    <div class="text-xs text-gray-500">{{ $transfer->toBlock->warehouse->name }}</div>
                </td>
                <td class="px-6 py-4 text-xs italic">{{ $transfer->trigger_reason ?? 'Không có lý do' }}</td>
                <td class="px-6 py-4">
                    @if($transfer->status == 'pending')
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Đang xử lý</span>
                    @elseif($transfer->status == 'completed')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Hoàn tất</span>
                    @else
                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold">{{ $transfer->status }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    @if($transfer->status == 'pending')
                        @can('complete', $transfer)
                        <form action="{{ route('transfers.complete', $transfer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Xác nhận đã chuyển hàng xong?')">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 shadow-sm">
                                <i class="fa-solid fa-check mr-1"></i> Hoàn tất
                            </button>
                        </form>
                        @endcan
                    @else
                        <span class="text-xs text-gray-400"><i class="fa-solid fa-lock"></i> Đã khóa</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-400">Chưa có lệnh chuyển kho nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $transfers->links() }}</div>
</div>
@endsection