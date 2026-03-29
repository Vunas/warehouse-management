@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Tiêu đề -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-800">Giỏ Hàng</h1>
        <p class="text-gray-600 mt-2">Xem và quản lý các sản phẩm trong giỏ hàng của bạn</p>
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

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800 font-bold mb-2"><i class="fa-solid fa-exclamation-circle mr-2"></i>Lỗi:</p>
            <p class="text-sm text-red-700 mb-3">{{ session('error') }}</p>
            <a href="{{ route('customer.address.create') }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded font-bold hover:bg-red-700 transition">
                <i class="fa-solid fa-plus mr-2"></i>Thêm Địa Chỉ Giao Hàng
            </a>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center">
            <i class="fa-solid fa-check-circle text-green-600 mr-3 text-xl"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($cartItems->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fa-solid fa-cart-shopping text-5xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Giỏ Hàng Trống</h2>
            <p class="text-gray-600 mb-6">Bạn chưa thêm sản phẩm nào vào giỏ hàng</p>
            <a href="{{ route('customer.dashboard') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>Tiếp tục mua sắm
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition">
                        <div class="flex items-center flex-1">
                            @if($item->product->images->first())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="Product" class="w-20 h-20 rounded object-cover mr-4">
                            @else
                                <div class="w-20 h-20 rounded bg-gray-200 flex items-center justify-center mr-4">
                                    <i class="fa-solid fa-image text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $item->product->brand->name ?? 'N/A' }}</p>
                                <p class="text-lg font-bold text-green-600 mt-1">{{ number_format($item->product->price, 0, ',', '.') }} ₫</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 ml-4">
                            <!-- Quantity -->
                            <form action="{{ route('customer.cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="1000" class="w-16 px-2 py-2 border border-gray-300 rounded text-center" required>
                                <button type="submit" title="Cập nhật" class="bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-2 rounded transition">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>

                            <!-- Subtotal -->
                            <div class="text-right">
                                <p class="text-xs text-gray-500 uppercase">Thành tiền</p>
                                <p class="text-lg font-black text-gray-800">{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }} ₫</p>
                            </div>

                            <!-- Remove -->
                            <form action="{{ route('customer.cart.remove', $item) }}" method="POST" onsubmit="return confirm('Xóa sản phẩm này khỏi giỏ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-600 hover:text-white px-3 py-2 rounded transition" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Tóm Tắt Đơn Hàng</h2>
                    
                    <div class="space-y-3 border-b border-gray-100 pb-4 mb-4">
                        <div class="flex justify-between text-gray-700">
                            <span>Số sản phẩm:</span>
                            <span class="font-bold">{{ $cartItems->count() }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Tổng số lượng:</span>
                            <span class="font-bold">{{ $cartItems->sum('quantity') }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-700">
                            <span>Tạm tính:</span>
                            <span class="font-bold">{{ number_format($total, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Phí vận chuyển:</span>
                            <span class="font-bold text-green-600">Miễn phí</span>
                        </div>
                        <div class="border-t border-gray-100 pt-3 flex justify-between">
                            <span class="font-bold text-gray-800">Tổng cộng:</span>
                            <span class="text-2xl font-black text-green-600">{{ number_format($total, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>

                    <form action="{{ route('customer.cart.checkout') }}" method="POST" class="space-y-3">
                        @csrf
                        <textarea name="notes" placeholder="Ghi chú đơn hàng (tuỳ chọn)" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        
                        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition">
                            <i class="fa-solid fa-check-circle mr-2"></i>Đặt Hàng
                        </button>
                    </form>

                    <a href="{{ route('customer.dashboard') }}" class="block text-center mt-3 text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
