@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="space-y-8 max-w-7xl mx-auto pb-10">
    
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Tổng quan Hệ thống Kho</h1>
            <p class="text-gray-500 mt-1">Theo dõi các chỉ số và hoạt động kho theo thời gian thực.</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-indigo-700 font-semibold bg-indigo-50 px-5 py-2.5 rounded-xl shadow-sm border border-indigo-100">
            <i class="fa-regular fa-clock animate-pulse"></i>
            Cập nhật lúc: {{ now()->format('H:i - d/m/Y') }}
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="relative bg-linear-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 shadow-lg shadow-blue-200 text-white transform transition duration-300 hover:-translate-y-1 hover:shadow-xl overflow-hidden">
            <div class="absolute -right-6 -top-6 opacity-20 text-8xl"><i class="fa-solid fa-box-open"></i></div>
            <p class="text-blue-100 font-medium uppercase tracking-wider text-sm mb-1">Mã Sản Phẩm</p>
            <h3 class="text-4xl font-black">{{ number_format($stats['total_products']) }}</h3>
            <p class="text-sm mt-3 text-blue-100"><i class="fa-solid fa-arrow-trend-up mr-1"></i> Tăng 5% so với tháng trước</p>
        </div>

        <div class="relative bg-linear-to-br from-emerald-400 to-teal-600 rounded-2xl p-6 shadow-lg shadow-teal-200 text-white transform transition duration-300 hover:-translate-y-1 hover:shadow-xl overflow-hidden">
            <div class="absolute -right-6 -top-6 opacity-20 text-8xl"><i class="fa-solid fa-boxes-stacked"></i></div>
            <p class="text-teal-100 font-medium uppercase tracking-wider text-sm mb-1">Tổng SP Tồn Kho</p>
            <h3 class="text-4xl font-black">{{ number_format($stats['total_inventory']) }}</h3>
            <p class="text-sm mt-3 text-teal-100"><i class="fa-solid fa-circle-check mr-1"></i> Sức chứa an toàn</p>
        </div>

        <div class="relative bg-linear-to-br from-amber-400 to-orange-500 rounded-2xl p-6 shadow-lg shadow-orange-200 text-white transform transition duration-300 hover:-translate-y-1 hover:shadow-xl overflow-hidden">
            <div class="absolute -right-6 -top-6 opacity-20 text-8xl"><i class="fa-solid fa-truck-arrow-right"></i></div>
            <p class="text-orange-100 font-medium uppercase tracking-wider text-sm mb-1">Phiếu chờ Nhập</p>
            <h3 class="text-4xl font-black">{{ number_format($stats['pending_inbounds']) }}</h3>
            <a href="#" class="inline-block mt-3 text-sm text-white hover:text-orange-200 font-medium hover:underline">Xem chi tiết <i class="fa-solid fa-arrow-right text-xs"></i></a>
        </div>

        <div class="relative bg-linear-to-br from-rose-500 to-red-600 rounded-2xl p-6 shadow-lg shadow-red-200 text-white transform transition duration-300 hover:-translate-y-1 hover:shadow-xl overflow-hidden">
            <div class="absolute -right-6 -top-6 opacity-20 text-8xl"><i class="fa-solid fa-truck-fast"></i></div>
            <p class="text-red-100 font-medium uppercase tracking-wider text-sm mb-1">Phiếu chờ Xuất</p>
            <h3 class="text-4xl font-black">{{ number_format($stats['pending_outbounds']) }}</h3>
            <a href="{{ route('outbounds.index', ['status' => 'pending']) }}" class="inline-block mt-3 text-sm text-white hover:text-red-200 font-medium hover:underline">Xử lý ngay <i class="fa-solid fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fa-solid fa-chart-line text-indigo-500 mr-2"></i>Lưu lượng Nhập / Xuất kho (7 ngày qua)</h3>
        <div id="inventoryChart" class="w-full h-80"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <span class="bg-red-100 text-red-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    Cảnh báo Sắp hết hàng
                </h3>
                <a href="{{ route('inventory.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition">Xem tất cả</a>
            </div>
            <div class="overflow-x-auto flex-1 p-2">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3">Sản phẩm</th>
                            <th class="px-4 py-3">Vị trí</th>
                            <th class="px-4 py-3 text-right">Tồn kho</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($lowStocks as $stock)
                            <tr class="hover:bg-slate-50 transition group">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 mr-3">
                                            <i class="fa-solid fa-image"></i> </div>
                                        <span class="text-sm font-bold text-gray-700 group-hover:text-indigo-600 transition">{{ $stock->product->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                        <i class="fa-solid fa-location-dot mr-1.5 text-gray-400"></i>{{ $stock->location->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="inline-flex items-center justify-center min-w-10 px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 animate-pulse">
                                        {{ $stock->quantity }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">Tuyệt vời! Không có sản phẩm nào sắp hết hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3"><i class="fa-solid fa-clipboard-list"></i></span>
                    Pick List Cần Xử Lý
                </h3>
            </div>
            <div class="overflow-x-auto flex-1 p-2">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3">Mã Phiếu</th>
                            <th class="px-4 py-3">Trạng thái</th>
                            <th class="px-4 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentTasks as $task)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-4">
                                    <p class="text-sm font-bold text-indigo-700">OUT-{{ str_pad($task->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-xs text-gray-500 mt-1"><i class="fa-regular fa-user mr-1"></i>{{ $task->staff->full_name ?? 'Hệ thống' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5"></span> Chờ xử lý
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">{{ $task->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('outbounds.show', $task->id) }}" class="inline-flex items-center bg-gray-900 text-white hover:bg-indigo-600 px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                                        Xử lý <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">Hiện không có phiếu xuất kho nào đang chờ.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            series: [{
                name: 'Nhập kho',
                data: [31, 40, 28, 51, 42, 109, 100] // Data mẫu, bạn sẽ truyền biến từ Controller vào đây
            }, {
                name: 'Xuất kho',
                data: [11, 32, 45, 32, 34, 52, 41]
            }],
            chart: {
                height: 320,
                type: 'area',
                fontFamily: 'inherit',
                toolbar: { show: false }
            },
            colors: ['#10b981', '#f43f5e'], // Xanh lá (Nhập) và Đỏ (Xuất)
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'], // Nhãn trục X
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af' } }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
            },
            legend: { position: 'top', horizontalAlign: 'right' }
        };

        var chart = new ApexCharts(document.querySelector("#inventoryChart"), options);
        chart.render();
    });
</script>
@endsection