@extends('layouts.admin')

@section('title', 'Quản lý Kho bãi')
@section('header', 'Danh sách Kho hàng')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <span class="text-sm text-gray-500">Quản lý cấu trúc kho và lô/kệ chứa hàng.</span>
        @can('create', App\Models\Warehouse::class)
        <a href="{{ route('warehouses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            <i class="fa-solid fa-plus mr-1"></i> Tạo Kho Mới
        </a>
        @endcan
    </div>

    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3">Tên Kho</th>
                <th class="px-6 py-3">Loại hình</th>
                <th class="px-6 py-3">Sức chứa</th>
                <th class="px-6 py-3">Trạng thái</th>
                <th class="px-6 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($warehouses as $wh)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <a href="{{ route('warehouses.show', $wh->id) }}" class="font-bold text-blue-600 hover:underline">
                        {{ $wh->name }}
                    </a>
                    <div class="text-xs text-gray-400">ID: {{ $wh->id }}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs border border-gray-200">
                        {{ $wh->type->type_code ?? 'N/A' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-xs mb-1">Tổng lô: <b>{{ $wh->blocks_count }}</b></div>
                    <div class="text-xs">Tổng slot: <b>{{ number_format($wh->total_slots) }}</b></div>
                </td>
                <td class="px-6 py-4">
                    @if($wh->status === 'active')
                        <span class="text-green-600 bg-green-50 px-2 py-1 rounded-full text-xs font-bold">Hoạt động</span>
                    @elseif($wh->status === 'maintenance')
                        <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full text-xs font-bold">Bảo trì</span>
                    @else
                        <span class="text-red-600 bg-red-50 px-2 py-1 rounded-full text-xs font-bold">Đóng cửa</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('warehouses.show', $wh->id) }}" class="text-gray-500 hover:text-blue-600" title="Xem sơ đồ"><i class="fa-solid fa-map"></i></a>
                    @can('update', $wh)
                    <a href="{{ route('warehouses.edit', $wh->id) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen"></i></a>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection