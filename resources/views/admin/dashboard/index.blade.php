@extends('layouts.admin')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <div class="space-y-6 max-w-7xl mx-auto pb-10 font-sans">

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tổng quan Hệ thống Kho</h1>
                <p class="text-sm text-slate-500 mt-1">Theo dõi các chỉ số và hoạt động kho theo thời gian thực.</p>
            </div>
            <div
                class="flex items-center gap-2 text-xs font-medium text-slate-500 bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm">
                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                Cập nhật lúc: <span
                    class="text-slate-700">{{ now()->timezone('Asia/Ho_Chi_Minh')->format('H:i - d/m/Y') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

            <div
                class="bg-white rounded-xl border border-slate-200 p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Mã Sản Phẩm</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ number_format($stats['total_products'] ?? 0) }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-emerald-600 font-medium bg-emerald-50 px-2 py-0.5 rounded flex items-center">
                        <i class="fa-solid fa-arrow-trend-up mr-1"></i> +5.2%
                    </span>
                    <span class="text-slate-400 ml-2">so với tháng trước</span>
                </div>
            </div>

            <div
                class="bg-white rounded-xl border border-slate-200 p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Tổng Tồn Kho</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ number_format($stats['total_inventory'] ?? 0) }}
                        </h3>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-slate-500 font-medium flex items-center">
                        <i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i> Mức lưu trữ an toàn
                    </span>
                </div>
            </div>

            <div
                class="bg-white rounded-xl border border-slate-200 p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Phiếu chờ Nhập</p>
                        <h3 class="text-3xl font-bold text-slate-900">{{ number_format($stats['pending_inbounds'] ?? 0) }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-truck-arrow-right"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="#"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center transition">
                        Xem chi tiết phiếu <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] flex flex-col justify-between group cursor-pointer hover:border-indigo-300 transition-colors"
                onclick="window.location.href='{{ route('outbounds.index', ['status' => 'pending']) }}'">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Phiếu chờ Xuất</p>
                        <h3 class="text-3xl font-bold text-rose-600">{{ number_format($stats['pending_outbounds'] ?? 0) }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span
                        class="text-xs font-semibold text-rose-600 group-hover:text-rose-700 flex items-center transition">
                        Xử lý ngay <i
                            class="fa-solid fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </div>
            </div>

        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] p-6">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-800">Lưu lượng Nhập / Xuất kho (7 ngày qua)</h3>
            </div>
            <div id="inventoryChart" class="w-full h-75"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div
                class="bg-white rounded-xl border border-slate-200 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center">
                        <div class="w-2 h-2 rounded-full bg-rose-500 mr-2 animate-pulse"></div>
                        Sắp hết hàng
                    </h3>
                    <a href="{{ route('inventory.index') }}"
                        class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">Xem tất cả</a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-white text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <th class="px-5 py-3 font-medium">Sản phẩm</th>
                                <th class="px-5 py-3 font-medium">Vị trí</th>
                                <th class="px-5 py-3 font-medium text-right">Tồn kho</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($lowStocks as $stock)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-3 align-middle">
                                        <span class="font-medium text-slate-800">{{ $stock->product->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-5 py-3 align-middle">
                                        <span
                                            class="inline-flex items-center text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded">
                                            <i
                                                class="fa-solid fa-location-dot mr-1.5 text-slate-400"></i>{{ $stock->location->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 align-middle text-right">
                                        <span
                                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                            {{ $stock->quantity }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">Không có cảnh
                                        báo tồn kho thấp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="bg-white rounded-xl border border-slate-200 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center">
                        <div class="w-2 h-2 rounded-full bg-amber-500 mr-2"></div>
                        Pick List Cần Xử Lý
                    </h3>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-white text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <th class="px-5 py-3 font-medium">Mã Phiếu</th>
                                <th class="px-5 py-3 font-medium">Trạng thái</th>
                                <th class="px-5 py-3 font-medium text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($recentTasks as $task)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-3 align-middle">
                                        <div class="font-semibold text-slate-800">
                                            OUT-{{ str_pad($task->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            {{ $task->staff->full_name ?? 'Hệ thống' }}</div>
                                    </td>
                                    <td class="px-5 py-3 align-middle">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                            Chờ xử lý
                                        </span>
                                        <div class="text-[11px] text-slate-400 mt-1">
                                            {{ $task->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-5 py-3 align-middle text-right">
                                        <a href="{{ route('outbounds.show', $task->id) }}"
                                            class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 hover:text-indigo-600 transition shadow-sm">
                                            Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">Hiện không có
                                        phiếu chờ xuất.</td>
                                </tr>
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
                    data: [31, 40, 28, 51, 42, 109, 100] // Tích hợp data động sau
                }, {
                    name: 'Xuất kho',
                    data: [11, 32, 45, 32, 34, 52, 41]
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#0ea5e9', '#6366f1'], // Xanh nhạt và Indigo chuẩn Enterprise
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.15,
                        opacityTo: 0.0,
                        stops: [0, 100]
                    }
                },
                xaxis: {
                    categories: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    markers: {
                        radius: 12
                    },
                    fontSize: '13px',
                    fontWeight: 500,
                    labels: {
                        colors: '#475569'
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + " đơn vị"
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#inventoryChart"), options);
            chart.render();
        });
    </script>
@endsection
