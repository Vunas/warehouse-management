@extends('layouts.admin')

@section('title', 'Báo cáo thống kê WMS')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header & Filter -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tổng quan Báo cáo & Thống kê</h1>
        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center space-x-2">
            <label class="text-sm text-gray-600">Thời gian:</label>
            <select name="days" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 ngày qua</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 ngày qua</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>3 tháng qua</option>
            </select>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Doanh thu</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($kpis['total_revenue']) }} đ</p>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Đơn hàng bán ra</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($kpis['total_orders']) }}</p>
            </div>
        </div>

        <!-- Inbound Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Phiếu nhập kho</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($kpis['total_inbounds']) }}</p>
            </div>
        </div>

        <!-- Low Stock Alert Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Mặt hàng cảnh báo</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($kpis['low_stock']) }}</p>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Chart (Takes up 2 columns on large screens) -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Biểu đồ doanh thu ({{ $days }} ngày)</h3>
            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Inventory Warnings -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Cảnh báo sắp hết hàng</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-lg">Sản phẩm</th>
                            <th class="px-4 py-3 text-right">Tồn kho</th>
                            <th class="px-4 py-3 text-right rounded-tr-lg">Ngưỡng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warnings as $warning)
                        <tr class="border-b">
                            <td class="px-4 py-3 font-medium text-gray-900 truncate max-w-[150px]">{{ $warning->name }}</td>
                            <td class="px-4 py-3 text-right text-red-600 font-bold">{{ $warning->current_stock }}</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ $warning->alert_threshold }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-500">Kho đang ở trạng thái an toàn.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="mt-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 5 Sản phẩm bán chạy nhất</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-lg">Hình ảnh</th>
                        <th class="px-4 py-3">Tên sản phẩm</th>
                        <th class="px-4 py-3 text-center">Số lượng bán</th>
                        <th class="px-4 py-3 text-right rounded-tr-lg">Tổng doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $product)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <img src="{{ $product->image_url ?? 'https://via.placeholder.com/40' }}" alt="img" class="w-10 h-10 rounded-md object-cover">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ number_format($product->total_sold) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-green-600 font-bold">{{ number_format($product->total_revenue) }} đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Nhúng thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Nhận dữ liệu từ Controller
        const labels = {!! json_encode($chartData['labels']) !!};
        const data = {!! json_encode($chartData['values']) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: data,
                    borderColor: '#3b82f6', // blue-500
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3 // Làm cong đường đồ thị
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Ẩn legend vì chỉ có 1 line
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if(value >= 1000000) return (value / 1000000) + ' Tr';
                                if(value >= 1000) return (value / 1000) + ' K';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush