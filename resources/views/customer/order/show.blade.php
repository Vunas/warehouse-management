@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Tiêu đề -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-800">Chi Tiết Đơn Hàng</h1>
                <p class="text-gray-600 mt-2">Mã đơn: <span class="font-bold">ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span></p>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-arrow-left mr-2"></i>Quay lại</a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800 font-bold mb-2"><i class="fa-solid fa-exclamation-circle mr-2"></i>Lỗi:</p>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center">
            <i class="fa-solid fa-check-circle text-green-600 mr-3 text-xl"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Order Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fa-solid fa-info-circle mr-2"></i>Trạng Thái Đơn</h3>
            
            @php
                $statusMap = [
                    'pending' => ['bg-yellow-100', 'text-yellow-800', 'Chờ Xử Lý', '⏳'],
                    'processing' => ['bg-blue-100', 'text-blue-800', 'Đang Xử Lý', '⚙️'],
                    'completed' => ['bg-green-100', 'text-green-800', 'Hoàn Thành', '✓'],
                    'cancelled' => ['bg-red-100', 'text-red-800', 'Đã Hủy', '✗'],
                ];
                $status = $statusMap[$order->status] ?? ['bg-gray-100', 'text-gray-800', 'Không Xác Định', '?'];
            @endphp

            <div class="text-center">
                <div class="text-5xl mb-2">{{ $status[3] }}</div>
                <span class="inline-block {{ $status[0] }} {{ $status[1] }} px-4 py-2 rounded-full font-bold">{{ $status[2] }}</span>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày đặt:</span>
                    <span class="font-bold">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Cập nhật:</span>
                    <span class="font-bold">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if(in_array($order->status, ['pending', 'processing']))
                <form action="{{ route('customer.order.cancel', $order) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg font-bold hover:bg-red-700 transition">
                        <i class="fa-solid fa-times-circle mr-2"></i>Hủy Đơn
                    </button>
                </form>
            @endif
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fa-solid fa-credit-card mr-2"></i>Thanh Toán</h3>
            
            @php
                $paymentStatus = $order->payment ? $order->payment->status : null;
                $paymentStatusMap = [
                    'pending' => ['bg-yellow-100', 'text-yellow-800', 'Chờ Thanh Toán'],
                    'completed' => ['bg-green-100', 'text-green-800', 'Đã Thanh Toán'],
                    'failed' => ['bg-red-100', 'text-red-800', 'Thanh Toán Thất Bại'],
                ];
                $pStatus = $paymentStatusMap[$paymentStatus] ?? ['bg-gray-100', 'text-gray-800', 'Không Xác Định'];
            @endphp

            <span class="inline-block {{ $pStatus[0] }} {{ $pStatus[1] }} px-3 py-1 rounded-full text-xs font-bold mb-4">{{ $pStatus[2] }}</span>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tổng tiền:</span>
                    <span class="text-2xl font-black text-green-600">{{ number_format($order->total_price, 0, ',', '.') }} ₫</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Phương thức:</span>
                    <span class="font-bold">
                        @if($order->payment->payment_method === 'bank_transfer')
                            Chuyển khoản ngân hàng
                        @elseif($order->payment->payment_method === 'vnpay')
                            VNPay
                        @elseif($order->payment->payment_method === 'cash')
                            Thanh toán khi nhận hàng
                        @else
                            Chưa chọn
                        @endif
                    </span>
                </div>
            </div>

            @if($paymentStatus === 'pending')
                <div class="mt-4 p-3 border-l-4 border-blue-500 bg-blue-50">
                    <p class="text-sm text-blue-800"><strong>Yêu cầu:</strong> Vui lòng chuyển khoản theo thông tin bên dưới:</p>
                    <div class="mt-2 text-sm text-blue-700">
                        <p><strong>Ngân hàng:</strong> Vietcombank</p>
                        <p><strong>Số tài khoản:</strong> 1234567890</p>
                        <p><strong>Tên chủ:</strong> Công ty Kho Hàng</p>
                        <p><strong>Nội dung:</strong> ORD{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fa-solid fa-list mr-2"></i>Tóm Tắt</h3>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Số sản phẩm:</span>
                    <span class="font-bold">{{ $order->items->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tổng số lượng:</span>
                    <span class="font-bold">{{ $order->items->sum('quantity') }}</span>
                </div>
                <div class="border-t border-gray-100 pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="font-bold">Tổng tiền:</span>
                        <span class="font-bold text-green-600">{{ number_format($order->total_price, 0, ',', '.') }} ₫</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-blue-50 border-b border-gray-100">
            <h2 class="text-lg font-bold text-blue-800"><i class="fa-solid fa-boxes-stacked mr-2"></i>Chi Tiết Sản Phẩm</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sản Phẩm</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Đơn Giá</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Số Lượng</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Thành Tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                <div class="flex items-center">
                                    @if($item->product->images->first())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="Product" class="w-10 h-10 rounded object-cover mr-3">
                                    @else
                                        <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fa-solid fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    {{ $item->product->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-center font-bold text-gray-800">{{ number_format($item->price, 0, ',', '.') }} ₫</td>
                            <td class="px-6 py-4 text-sm text-center font-bold text-gray-800">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-green-600">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} ₫</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notes -->
    @if($order->notes)
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3"><i class="fa-solid fa-note-sticky mr-2"></i>Ghi Chú</h3>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
    @endif
</div>
@endsection
