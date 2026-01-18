@extends('layouts.admin')

@section('title', 'Quản lý Nhập kho')
@section('header', 'Danh sách Phiếu Nhập Kho')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <div class="flex gap-2">
            <form action="" method="GET" class="relative">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                <input type="text" name="search" placeholder="Mã phiếu, Hợp đồng..." class="pl-8 pr-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </form>
        </div>
        @can('create',App\Models\InboundTicket::class)
        <a href="{{ route('inbound_tickets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 shadow-sm">
            <i class="fa-solid fa-plus mr-1"></i> Tạo Phiếu Nhập
        </a>
        @endcan
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 uppercase text-xs text-gray-500 font-semibold">
                <tr>
                    <th class="px-6 py-3">Mã phiếu</th>
                    <th class="px-6 py-3">Hợp đồng</th>
                    <th class="px-6 py-3">Khách hàng</th>
                    <th class="px-6 py-3">Ngày dự kiến</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('inbound_tickets.show', $ticket->id) }}" class="font-bold text-blue-600 hover:underline">
                            #IN-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs">{{ $ticket->contract->contract_code ?? '---' }}</td>
                    <td class="px-6 py-4">{{ $ticket->contract->customer->company_name ?? '---' }}</td>
                    <td class="px-6 py-4">{{ $ticket->expected_date ? $ticket->expected_date->format('d/m/Y') : '' }}</td>
                    <td class="px-6 py-4">
                        @if($ticket->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Chờ duyệt</span>
                        @elseif($ticket->status == 'approved')
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Đã duyệt</span>
                        @elseif($ticket->status == 'received')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Đã nhập kho</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Từ chối</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('inbound_tickets.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1 rounded text-xs">
                            Chi tiết <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        <i class="fa-solid fa-box-open text-2xl mb-2"></i><br>
                        Chưa có phiếu nhập kho nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-100">
        {{ $tickets->links() }}
    </div>
</div>
@endsection