@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">Chi tiết Đơn hàng #ORD-{{ $order->id }}</h1>
                    @php
                        $badgeColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-indigo-100 text-indigo-800',
                            'shipping' => 'bg-purple-100 text-purple-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'pending' => 'Mới tạo (Pending)', 
                            'processing' => 'Đang xử lý', 'shipping' => 'Đang giao (Shipping)', 
                            'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'
                        ];
                        $isLocked = in_array($order->status, ['completed', 'cancelled']);
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badgeColors[$order->status] ?? 'bg-gray-100' }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                    @if($isLocked)
                        <span class="text-xs text-gray-400 italic">🔒 Đã chốt, không thể thay đổi</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">Ngày đặt: {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
            </div>
            
            <!-- Hành động chung -->
            <div class="flex items-center space-x-4 bg-white p-2 rounded-lg shadow-sm border border-gray-100">
                <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-900 text-sm font-medium px-3">
                    &larr; Trở về
                </a>
                
                <div class="h-6 w-px bg-gray-200"></div>

                <!-- Cập nhật trạng thái tổng -->
                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="flex items-center gap-2 m-0">
                    @csrf
                    <select name="status" class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-1.5 font-medium {{ $isLocked ? 'bg-gray-100 cursor-not-allowed text-gray-500' : '' }}" {{ $isLocked ? 'disabled' : '' }}>
                        @foreach($statusLabels as $val => $label)
                            <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex items-center px-4 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed" {{ $isLocked ? 'disabled' : '' }}>
                        Cập nhật
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-md bg-green-50 border border-green-200 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3 font-medium text-sm text-green-800">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-md bg-red-50 border border-red-200 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3 font-medium text-sm text-red-800">{{ session('error') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Cột trái: Thông tin Khách & Giao dịch -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Card 1: Khách hàng -->
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                    <div class="px-4 py-4 sm:px-6 bg-gray-50/50 border-b border-gray-200">
                        <h3 class="text-base font-bold text-gray-900">Thông tin Khách hàng</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tên khách hàng</p>
                            <p class="mt-1 text-sm font-bold text-gray-900">{{ $order->user->name ?? 'Tài khoản đã xóa' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email & SĐT</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $order->user->email ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-900">{{ $order->user->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-sm font-medium text-gray-500">Địa chỉ giao hàng</p>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($address)
                                    {{ $address->detail }}, {{ $address->ward->name ?? '' }}, {{ $address->ward->district->name ?? '' }}
                                @else
                                    <span class="italic text-gray-400">Không có thông tin địa chỉ</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Lịch sử Giao dịch -->
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                    <div class="px-4 py-4 sm:px-6 bg-gray-50/50 border-b border-gray-200">
                        <h3 class="text-base font-bold text-gray-900">Lịch sử Thanh toán</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        @php
                            $paymentList = $order->payments ?? ($order->payment ? [$order->payment] : []);
                        @endphp
                        @forelse($paymentList as $payment)
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-bold uppercase {{ $payment->status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ $payment->status === 'paid' ? 'Thành công' : 'Chờ TT' }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->created_at)->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-sm mt-1 pt-1 border-t border-gray-200 flex justify-between items-center">
                                    <span class="font-medium text-gray-500 uppercase">{{ $payment->payment_method }}</span> 
                                    <span class="font-black text-gray-900">{{ number_format($payment->amount, 0, ',', '.') }} đ</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic text-center py-4">Chưa có giao dịch.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Cột phải: Chi tiết sản phẩm & Hành động Từ chối -->
            <div class="lg:col-span-2">
                <!-- BOX ACTION: TỪ CHỐI TOÀN BỘ ĐƠN HÀNG -->
                @if(!$isLocked && $order->status !== 'shipping')
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-5 shadow-sm">
                        <h3 class="text-red-800 font-bold mb-2">Từ chối Đơn hàng</h3>
                        <p class="text-sm text-red-600 mb-4">Thao tác này sẽ hủy đơn hàng, nhả tồn kho đã giữ chỗ (Reserved Stock) và trả lại tất cả sản phẩm vào Giỏ hàng của khách.</p>
                        
                        <form action="{{ route('orders.rejectOrder', $order->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy và từ chối TOÀN BỘ đơn hàng này chứ?')">
                            @csrf
                            @method('DELETE')
                            <div class="flex gap-3">
                                <input type="text" name="reason" placeholder="Nhập lý do từ chối (bắt buộc)..." required class="flex-1 rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm text-red-900 bg-white">
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold text-sm rounded-md shadow hover:bg-red-700 transition">
                                    Từ chối & Báo Email
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                    <div class="px-4 py-4 sm:px-6 bg-gray-50/50 border-b border-gray-200">
                        <h3 class="text-base font-bold text-gray-900">Danh sách Sản phẩm</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sản phẩm</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Đơn giá</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Số lượng</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($order->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</div>
                                            <div class="text-xs text-gray-500 mt-1">Mã SP: #{{ $item->product_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-600">
                                            {{ number_format($item->price, 0, ',', '.') }} đ
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-black text-indigo-600">
                                            x{{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Đơn hàng trống.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Tạm Tính & Tổng Tiền -->
                    <div class="bg-gray-50 px-6 py-5 border-t border-gray-200">
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-end items-center">
                                <dt class="font-medium text-gray-500 w-32 text-right">Tạm tính:</dt>
                                <dd class="text-gray-900 w-32 text-right">{{ number_format($order->total_price, 0, ',', '.') }} đ</dd>
                            </div>
                            <div class="flex justify-end items-center">
                                <dt class="font-medium text-gray-500 w-32 text-right">Vận chuyển:</dt>
                                <dd class="text-gray-900 w-32 text-right">0 đ</dd>
                            </div>
                            <div class="flex justify-end items-center border-t border-gray-200 pt-3 mt-3">
                                <dt class="text-base font-bold text-gray-900 w-40 text-right">Tổng thanh toán:</dt>
                                <dd class="text-xl font-black text-red-600 w-40 text-right">{{ number_format($order->total_price, 0, ',', '.') }} đ</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection