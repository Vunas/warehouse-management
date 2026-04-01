@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chi tiết đơn hàng #ORD-{{ $order->id }}</h1>
                <p class="text-sm text-gray-500">Ngày đặt:
                    {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-900">
                    &larr; Quay lại danh sách
                </a>
                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <select name="status"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Đang giao</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Cập nhật
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1 space-y-6">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin khách hàng</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Họ và tên</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->user->full_name ?? 'Khách vô danh' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->user->phone ?? 'Không có' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->user->email ?? 'Không có' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Địa chỉ giao hàng</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @if ($order->address)
                            <p class="text-sm text-gray-900">{{ $order->address->street }}</p>
                            <p class="text-sm text-gray-900">{{ $order->address->ward }}, {{ $order->address->district }},
                                {{ $order->address->city }}</p>
                        @else
                            <p class="text-sm text-gray-500 italic">Không có thông tin địa chỉ.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Chi tiết sản phẩm</h3>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sản phẩm</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Đơn giá</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Số lượng</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $item->product->name ?? 'Sản phẩm đã xóa' }}
                                                </div>

                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ number_format($item->price, 0, ',', '.') }} đ
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        x{{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Không có sản phẩm nào trong đơn hàng này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="bg-gray-50 px-4 py-5 sm:p-6 border-t border-gray-200">
                        <dl class="space-y-2 text-right">
                            <div class="flex justify-end text-sm">
                                <dt class="font-medium text-gray-500 w-32">Tạm tính:</dt>
                                <dd class="text-gray-900 w-32">{{ number_format($order->total_price, 0, ',', '.') }} đ</dd>
                            </div>
                            <div class="flex justify-end text-sm">
                                <dt class="font-medium text-gray-500 w-32">Phí vận chuyển:</dt>
                                <dd class="text-gray-900 w-32">0 đ</dd>
                            </div>
                            <div class="flex justify-end text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                                <dt class="text-gray-900 w-32">Tổng cộng:</dt>
                                <dd class="text-red-600 w-32">{{ number_format($order->total_price, 0, ',', '.') }} đ</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
