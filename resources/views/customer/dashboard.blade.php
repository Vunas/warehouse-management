@extends('layouts.customer')

@section('title', 'Tổng quan hệ thống')
@section('header', 'Dashboard Quản Lý')

@section('content')
<!-- 1. Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Phiếu chờ nhập[xong]-->
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Phiếu chờ nhập</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['pending_inbound'] }}</h3>
            </div>
            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-gray-500">
            Đang chờ xử lý
        </div>
    </div>
    <!-- Card 2: Phiếu chờ xuất[xong]-->
       <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Phiếu chờ xuất</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['pending_outbound'] }}</h3>
            </div>
            <div class="p-2 bg-blue-100 rounded-lg text-red-600">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-gray-500">
            Đang chờ xử lý
        </div>
    </div>

    <!-- Card 3: Hợp đồng Active[xong] -->
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Hợp đồng Active</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['active_contracts'] }}</h3>
            </div>
            <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                <i class="fa-solid fa-file-contract"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-gray-500">
            Đang thuê kho
        </div>
    </div>

    <!-- Card 4: Slot Trống[chưa xong] -->
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Slot đang sử dụng</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['free_slots']) }}</h3>
            </div>
            <div class="p-2 bg-green-100 rounded-lg text-green-600">
                <i class="fa-solid fa-cubes-stacked"></i>
            </div>
        </div>
    </div>


</div>

<!-- 2. Main Content Grid[chưa xong] -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Inbound Queue -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Yêu cầu nhập kho mới nhất</h2>
                <a href="#" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                        <tr>
                            <th class="px-6 py-3">Mã hợp đồng</th>
                            <th class="px-6 py-3">Khách hàng</th>
                            <th class="px-6 py-3">Dự kiến</th>
                            <th class="px-6 py-3">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($latestInbounds as $ticket)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-blue-600">
                                {{ $ticket->contract->contract_code ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $ticket->contract->customer->company_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $ticket->expected_date ? $ticket->expected_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->status == 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-semibold">Chờ duyệt</span>
                                @elseif($ticket->status == 'approved')
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">Đã duyệt</span>
                                @elseif($ticket->status == 'received')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Đã nhập</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Từ chối</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                Chưa có phiếu nhập nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Right Column: Quick Actions -->
    <div class="space-y-8">
        <div class="bg-linear-to-br from-blue-600 to-blue-800 rounded-xl shadow-lg p-6 text-white">
            <h2 class="text-lg font-bold mb-4">Thao tác nhanh</h2>
            <div class="space-y-3">
                <a href="{{ route('employees.create') }}" class="block w-full bg-white/20 hover:bg-white/30 p-3 rounded-lg items-center transition">
                    <i class="fa-solid fa-user-plus w-8 text-xl"></i>
                    <div class="text-left">
                        <div class="font-bold text-sm">Nhập hàng</div>
                        <div class="text-xs text-blue-100">Tạo phiếu nhập</div>
                    </div>
                </a>
                
                <!-- Placeholder cho các tính năng khác chưa implement -->
                <button class="w-full bg-white/20 hover:bg-white/30 p-3 rounded-lg flex items-center transition opacity-75 cursor-not-allowed">
                    <i class="fa-solid fa-file-signature w-8 text-xl"></i>
                    <div class="text-left">
                        <div class="font-bold text-sm">Nhập nhiều món hàng cùng lúc (Sắp ra mắt)</div>
                        <div class="text-xs text-blue-100">nhập hàng vào kho</div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection