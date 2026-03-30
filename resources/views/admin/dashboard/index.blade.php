@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Tiêu đề -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-gray-800">Tổng quan Hệ thống Kho</h1>
        <div class="text-sm text-gray-500 font-medium bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
            <i class="fa-regular fa-clock mr-2"></i>Cập nhật lúc: {{ now()->format('H:i - d/m/Y') }}
        </div>
    </div>

    <!-- Hàng 1: Các Widget Thống Kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tổng Sản Phẩm -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-2xl mr-4">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase">Mã Sản Phẩm</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($stats['total_products']) }}</h3>
            </div>
        </div>

        <!-- Tổng Tồn Kho -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 text-2xl mr-4">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase">Tổng SP Tồn Kho</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($stats['total_inventory']) }}</h3>
            </div>
        </div>

        <!-- Chờ Nhập Kho -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-600 text-2xl mr-4">
                <i class="fa-solid fa-truck-arrow-right"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase">Phiếu chờ Nhập</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($stats['pending_inbounds']) }}</h3>
            </div>
        </div>

        <!-- Chờ Xuất Kho -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center text-red-600 text-2xl mr-4">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase">Phiếu chờ Xuất</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($stats['pending_outbounds']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Hàng 2: Bảng Cảnh báo & Công việc -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Bảng 1: Cảnh báo Tồn kho thấp -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-red-800"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Cảnh báo Sắp hết hàng</h3>
                <a href="{{ route('inventory.index') }}" class="text-sm font-medium text-red-600 hover:text-red-800">Xem tất cả</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Vị trí</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Tồn kho</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lowStocks as $stock)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $stock->product->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $stock->location->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-center font-black text-red-600">{{ $stock->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Tuyệt vời! Không có sản phẩm nào sắp hết hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bảng 2: Phiếu xuất đang chờ xử lý -->
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-blue-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-blue-800"><i class="fa-solid fa-clipboard-list mr-2"></i>Phiếu xuất kho (Pick List) cần xử lý</h3>
                <a href="{{ route('outbounds.index', ['status' => 'pending']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Xem tất cả</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mã Phiếu</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Người lập</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Xử lý</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-bold text-indigo-700">OUT-{{ str_pad($task->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $task->staff->full_name ?? 'N/A' }}<br><span class="text-xs text-gray-400">{{ $task->created_at->diffForHumans() }}</span></td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('outbounds.show', $task->id) }}" class="inline-block bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-3 py-1 rounded text-xs font-bold transition">Tiến hành</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Không có phiếu xuất kho nào đang chờ.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection