@extends('layouts.admin')

@section('title', 'Quản lý Hợp đồng')
@section('header', 'Danh sách Hợp đồng Thuê')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <form action="" method="GET" class="flex gap-2 relative">
            <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            <input type="text" name="search" placeholder="Mã HĐ, Khách hàng..." class="pl-8 pr-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-blue-500 w-64">
        </form>
        @can('create', App\Models\Contract::class)
        <a href="{{ route('contracts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 shadow-sm">
            <i class="fa-solid fa-plus mr-1"></i> Tạo Hợp đồng
        </a>
        @endcan
    </div>

    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3">Mã HĐ</th>
                <th class="px-6 py-3">Khách hàng</th>
                <th class="px-6 py-3">Thời hạn</th>
                <th class="px-6 py-3">Trạng thái</th>
                <th class="px-6 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($contracts as $contract)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-mono text-blue-600 font-bold">
                    <a href="{{ route('contracts.show', $contract->id) }}" class="hover:underline">{{ $contract->contract_code }}</a>
                </td>
                <td class="px-6 py-4 font-medium">{{ $contract->customer->company_name ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-xs">
                    <div>{{ $contract->start_date->format('d/m/Y') }}</div>
                    <div class="text-gray-400">đến {{ $contract->end_date->format('d/m/Y') }}</div>
                </td>
                <td class="px-6 py-4">
                    @if($contract->status == 'active')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Hiệu lực</span>
                    @elseif($contract->status == 'expired')
                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold">Hết hạn</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Đình chỉ</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('contracts.show', $contract->id) }}" class="text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1 rounded text-xs">
                        Chi tiết <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Chưa có hợp đồng nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $contracts->links() }}</div>
</div>
@endsection