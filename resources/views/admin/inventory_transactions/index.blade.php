@extends('layouts.admin')

@section('content')
    <div class="max-w-[90rem] mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    Sổ Kho (Lịch sử Giao dịch)
                </h2>
                <p class="text-sm text-slate-500 mt-1">Quản lý và tra cứu mọi biến động Nhập, Xuất, Chuyển kệ, Kiểm kê.</p>
            </div>
        </div>

        <!-- Filter Card -->
        <div
            class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    <h3 class="text-sm font-semibold text-slate-800">Bộ lọc Tìm kiếm</h3>
                </div>
                <!-- Nút bật/tắt Lọc nâng cao -->
                <button type="button" id="toggleAdvancedFilter"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
                    <span
                        id="advancedFilterText">{{ $hasAdvancedFilters ? 'Ẩn bộ lọc nâng cao' : 'Hiện bộ lọc nâng cao' }}</span>
                    <svg id="advancedFilterIcon"
                        class="w-4 h-4 transform transition-transform duration-200 {{ $hasAdvancedFilters ? 'rotate-180' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <form action="{{ route('inventory_transactions.index') }}" method="GET">
                    <!-- Lọc Cơ Bản -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-5 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Loại Biến Động</label>
                            <select name="type"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                                <option value="">-- Tất cả giao dịch --</option>
                                <option value="inbound" {{ request('type') == 'inbound' ? 'selected' : '' }}>🟢 Nhập Kho
                                </option>
                                <option value="outbound" {{ request('type') == 'outbound' ? 'selected' : '' }}>🔴 Xuất Kho
                                </option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>🔵 Chuyển Kho
                                </option>
                                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>🟣 Kiểm
                                    kê / Điều chỉnh</option>
                            </select>
                        </div>

                        <div class="md:col-span-5">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tìm Sản phẩm</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Nhập tên SP hoặc ID (VD: SP-1)..."
                                    class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                            </div>
                        </div>

                        <!-- Nút thao tác nằm cùng hàng cơ bản -->
                        <div class="md:col-span-3 flex items-center justify-end gap-2">
                            @if (request()->anyFilled(['type', 'search', 'date_from', 'date_to', 'price_from', 'price_to']))
                                <a href="{{ route('inventory_transactions.index') }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-slate-600 text-sm font-medium rounded-lg border border-slate-300 hover:bg-slate-50 hover:text-red-600 transition-all w-full md:w-auto">
                                    Xóa lọc
                                </a>
                            @endif
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all w-full md:w-auto">
                                Lọc Dữ Liệu
                            </button>
                        </div>
                    </div>

                    <!-- Lọc Nâng Cao (Toggleable) -->
                    <div id="advancedFilters"
                        class="grid grid-cols-1 md:grid-cols-12 gap-5 pt-5 border-t border-slate-100 {{ $hasAdvancedFilters ? '' : 'hidden' }}">
                        <!-- Ngày tháng -->
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Từ ngày</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Đến ngày</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>

                        <!-- Giá tiền -->
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Giá SP từ (VNĐ)</label>
                            <input type="number" name="price_from" min="0" value="{{ request('price_from') }}"
                                placeholder="0"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Đến (VNĐ)</label>
                            <input type="number" name="price_to" min="0" value="{{ request('price_to') }}"
                                placeholder="Vô hạn"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng Dữ Liệu Sổ Kho -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Thời Gian</th>
                            <th
                                class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Loại & ID</th>
                            <th
                                class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Sản phẩm & Lô</th>
                            <th
                                class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Vị Trí Cập Nhật</th>
                            <th
                                class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                SL Thay Đổi</th>
                            <th
                                class="px-5 py-3.5 text-center text-xs font-semibold text-indigo-600 uppercase tracking-wider bg-indigo-50/50 border-x border-indigo-100">
                                Tồn Cuối</th>
                            <th
                                class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Nhân sự / Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($transactions as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Thời gian -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600">
                                    <span
                                        class="block text-slate-800 font-bold">{{ $log->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</span>
                                    <span
                                        class="text-xs text-slate-500">{{ $log->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i:s') }}</span>
                                </td>

                                <!-- Loại giao dịch -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($log->transaction_type == 'inbound')
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-md text-xs font-bold border border-emerald-200"><span
                                                class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Nhập kho</span>
                                    @elseif($log->transaction_type == 'outbound')
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-rose-100 text-rose-700 px-2.5 py-1 rounded-md text-xs font-bold border border-rose-200"><span
                                                class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Xuất kho</span>
                                    @elseif($log->transaction_type == 'transfer')
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 px-2.5 py-1 rounded-md text-xs font-bold border border-blue-200"><span
                                                class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Chuyển kho</span>
                                    @elseif($log->transaction_type == 'adjustment')
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-purple-100 text-purple-700 px-2.5 py-1 rounded-md text-xs font-bold border border-purple-200"><span
                                                class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>Kiểm kê</span>
                                    @endif
                                    <div class="text-[11px] text-slate-400 mt-1.5 font-mono">Ref:
                                        #{{ $log->reference_id ?? 'N/A' }}</div>
                                </td>

                                <!-- Sản phẩm & Lô -->
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-800 line-clamp-1">
                                        {{ $log->product->name ?? 'N/A' }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-slate-500">Mã: SP-{{ $log->product_id }}</span>
                                        <span class="text-[10px] text-slate-400">•</span>
                                        <span class="text-xs text-slate-500">Giá:
                                            {{ number_format($log->product->price ?? 0, 0, ',', '.') }}đ</span>
                                    </div>
                                    @if ($log->batch)
                                        <span
                                            class="text-[11px] bg-slate-100 border border-slate-200 px-2 py-0.5 rounded font-mono text-slate-600 mt-1 inline-block">Lô:
                                            {{ $log->batch->batch_code }}</span>
                                    @endif
                                </td>

                                <!-- Vị trí -->
                                <td class="px-5 py-4 text-sm">
                                    <p class="font-medium text-slate-800">{{ $log->location->warehouse->name ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-indigo-600 font-medium mt-0.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        Kệ: {{ $log->location->name ?? 'N/A' }}
                                    </p>
                                </td>

                                <!-- Số lượng thay đổi -->
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    @if ($log->quantity_change > 0)
                                        <span
                                            class="inline-flex items-center justify-center min-w-12 text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-md">+{{ $log->quantity_change }}</span>
                                    @elseif($log->quantity_change < 0)
                                        <span
                                            class="inline-flex items-center justify-center min-w-12 text-sm font-bold text-rose-700 bg-rose-50 border border-rose-100 px-2 py-1 rounded-md">{{ $log->quantity_change }}</span>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center min-w-12 text-sm font-bold text-slate-500 bg-slate-50 border border-slate-200 px-2 py-1 rounded-md">0</span>
                                    @endif
                                </td>

                                <!-- Số tồn cuối -->
                                <td
                                    class="px-5 py-4 whitespace-nowrap text-center bg-indigo-50/20 border-x border-indigo-50/50">
                                    <span class="text-lg font-black text-indigo-700">{{ $log->balance_after }}</span>
                                </td>

                                <!-- Nhân sự & Note -->
                                <td class="px-5 py-4 text-sm text-slate-600">
                                    <div class="flex items-center gap-1.5 font-medium text-slate-800 mb-1">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        {{ $log->staff->name ?? 'Hệ thống' }}
                                    </div>
                                    <span
                                        class="text-xs italic text-slate-500 block max-w-50 wrap-break-word">"{{ $log->note ?? 'Trống' }}"</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                    <p class="text-slate-500 font-medium">Không tìm thấy giao dịch nào phù hợp với bộ lọc.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $transactions->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Script cho tính năng ẩn hiện lọc nâng cao -->
    @push('scripts')
        <!-- Nhúng thư viện Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // 1. BIỂU ĐỒ DOANH THU (Line Chart)
                const revCtx = document.getElementById('revenueChart').getContext('2d');
                const revLabels = {!! json_encode($revenueChart['labels']) !!};
                const revData = {!! json_encode($revenueChart['values']) !!};

                new Chart(revCtx, {
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
                            legend: {
                                display: false
                            },
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
                                suggestedMax: 1000000, // Đặt mức max ảo để biểu đồ ko bị lơ lửng giữa trời khi data toàn = 0
                                ticks: {
                                    callback: function(value) {
                                        if (value === 0) return '0 đ';
                                        if (value >= 1000000) return (value / 1000000) + ' Tr';
                                        if (value >= 1000) return (value / 1000) + ' K';
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

                // 2. BIỂU ĐỒ NHẬP XUẤT KHO (Bar Chart)
                const inOutCtx = document.getElementById('inOutChart').getContext('2d');
                const ioLabels = {!! json_encode($inOutChart['labels']) !!};
                const inData = {!! json_encode($inOutChart['in_values']) !!};
                const outData = {!! json_encode($inOutChart['out_values']) !!};

                new Chart(inOutCtx, {
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
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 10, // Mức max ảo, nếu data cao hơn 10 thì chart tự giãn theo data
                                ticks: {
                                    stepSize: 1
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
@endsection
