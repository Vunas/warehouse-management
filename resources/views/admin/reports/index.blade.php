@extends('layouts.admin')

@section('title', 'Báo cáo thống kê WMS')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-[90rem]">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Tổng quan Báo cáo & Thống kê</h1>
                <p class="text-sm text-slate-500">Xem dữ liệu hệ thống theo khoảng thời gian tùy chọn.</p>
            </div>

            <form method="GET" action="{{ route('reports.index') }}"
                class="flex items-center gap-2 bg-white p-2 rounded-lg shadow-sm border border-slate-200">
                <div class="flex items-center gap-2 px-2">
                    <span class="text-sm font-medium text-slate-600">Từ</span>
                    <input type="date" name="start_date" value="{{ $startDateInput ?? '' }}"
                        class="border-slate-300 rounded-md text-sm focus:ring-indigo-500 py-1.5 px-3">
                </div>
                <div class="flex items-center gap-2 px-2 border-l border-slate-100">
                    <span class="text-sm font-medium text-slate-600">Đến</span>
                    <input type="date" name="end_date" value="{{ $endDateInput ?? '' }}"
                        class="border-slate-300 rounded-md text-sm focus:ring-indigo-500 py-1.5 px-3">
                </div>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-md text-sm font-medium transition-colors shadow-sm ml-1">
                    Lọc
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex items-center">
                <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600 mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Doanh thu bán ra</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($kpis['total_revenue'] ?? 0) }} đ</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex items-center">
                <div class="p-3 rounded-xl bg-blue-100 text-blue-600 mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Đơn hàng thành công</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($kpis['total_orders'] ?? 0) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex items-center">
                <div class="p-3 rounded-xl bg-purple-100 text-purple-600 mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Phiếu nhập kho</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($kpis['total_inbounds'] ?? 0) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex items-center">
                <div class="p-3 rounded-xl bg-rose-100 text-rose-600 mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Cảnh báo sắp hết</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($kpis['low_stock'] ?? 0) }} SP</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Biểu đồ doanh thu</h3>
                <div class="relative h-72 w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Biểu đồ Nhập / Xuất kho (Số lượng)</h3>
                <div class="relative h-72 w-full">
                    <canvas id="inOutChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden lg:col-span-1">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-800">Top 5 SP Bán chạy</h3>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-slate-100">
                        @forelse($topProducts as $product)
                            <li class="p-4 hover:bg-slate-50 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/40' }}"
                                        class="w-10 h-10 rounded-lg object-cover border border-slate-200">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 line-clamp-1">{{ $product->name }}</p>
                                        <p class="text-xs text-emerald-600 font-semibold">
                                            {{ number_format($product->total_revenue) }} đ</p>
                                    </div>
                                </div>
                                <span
                                    class="bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-md">{{ number_format($product->total_sold) }}
                                    bán</span>
                            </li>
                        @empty
                            <li class="p-8 text-center text-slate-500 text-sm">Chưa có dữ liệu bán hàng.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden lg:col-span-1">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800">Hoạt động Nhân viên</h3>
                    <span class="text-xs text-slate-500">Số thao tác trên hệ thống</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 font-bold">Nhân viên</th>
                                <th class="px-4 py-3 font-bold text-center">Tổng</th>
                                <th class="px-4 py-3 font-bold text-center" title="Số lần Nhập/Xuất/Chuyển">N/X/C</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($staffActivities as $staff)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-800 flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                            {{ substr($staff->username, 0, 1) }}</div>
                                        {{ $staff->username }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-700">
                                        {{ $staff->total_transactions }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-slate-500 font-mono">
                                        <span class="text-emerald-600">{{ $staff->inbound_count }}</span> /
                                        <span class="text-rose-600">{{ $staff->outbound_count }}</span> /
                                        <span class="text-blue-600">{{ $staff->transfer_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-slate-500">Không có hoạt động nào
                                        trong thời gian này.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden lg:col-span-1">
                <div class="p-5 border-b border-slate-100 bg-rose-50/50">
                    <h3 class="text-base font-bold text-rose-800 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        Sắp hết hàng
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 font-bold">Sản phẩm</th>
                                <th class="px-4 py-3 font-bold text-right">Tồn</th>
                                <th class="px-4 py-3 font-bold text-right">Ngưỡng</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($warnings as $warning)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-800 line-clamp-1"
                                        title="{{ $warning->name }}">{{ $warning->name }}</td>
                                    <td class="px-4 py-3 text-right text-rose-600 font-bold bg-rose-50/30">
                                        {{ $warning->current_stock }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">{{ $warning->alert_threshold }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="px-4 py-8 text-center text-emerald-600 font-medium bg-emerald-50/30">Kho
                                        đang ở trạng thái an toàn.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. BIỂU ĐỒ DOANH THU (Line Chart)
            const revCtx = document.getElementById('revenueChart');
            if (revCtx) {
                const revLabels = {!! json_encode($revenueChart['labels'] ?? []) !!};
                const revData = {!! json_encode($revenueChart['values'] ?? []) !!};

                new Chart(revCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: revLabels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: revData,
                            borderColor: '#4f46e5', // indigo-600
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return new Intl.NumberFormat('vi-VN', {
                                            style: 'currency',
                                            currency: 'VND'
                                        }).format(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value === 0) return '0 đ';
                                        if (value >= 1000000) return (value / 1000000) + ' Tr';
                                        if (value >= 1000) return (value / 1000) + ' K';
                                        return value;
                                    }
                                }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // 2. BIỂU ĐỒ NHẬP XUẤT KHO (Bar Chart)
            const inOutCtx = document.getElementById('inOutChart');
            if (inOutCtx) {
                const ioLabels = {!! json_encode($inOutChart['labels'] ?? []) !!};
                const inData = {!! json_encode($inOutChart['in_values'] ?? []) !!};
                const outData = {!! json_encode($inOutChart['out_values'] ?? []) !!};

                new Chart(inOutCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ioLabels,
                        datasets: [{
                                label: 'Nhập kho (SL)',
                                data: inData,
                                backgroundColor: '#10b981', // emerald-500
                                borderRadius: 4,
                            },
                            {
                                label: 'Xuất kho (SL)',
                                data: outData,
                                backgroundColor: '#f43f5e', // rose-500
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        });
    </script>
@endsection