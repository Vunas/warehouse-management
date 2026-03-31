@extends('layouts.customer')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Tiêu đề -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-gray-800">Nhập hàng</h1>
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
                <p class="text-sm font-bold text-gray-500 uppercase">Tổng Sản Phẩm</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($products->count()) }}</h3>
            </div>
        </div>

        <!-- Sản Phẩm Còn Hàng -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 text-2xl mr-4">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase">Còn Hàng</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($products->where('status', 'Còn hàng')->count()) }}</h3>
            </div>
        </div>

        <!-- Giỏ Hàng -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-600 text-2xl mr-4">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase">Giỏ Hàng</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($cartStats['cart_items']) }}</h3>
            </div>
        </div>

        <!-- Tổng Tiền Giỏ -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-600 text-2xl mr-4">
                <i class="fa-solid fa-money-bill"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase">Tổng Tiền</p>
                <h3 class="text-3xl font-black text-gray-800 mt-1">{{ number_format($cartStats['cart_total'], 0, ',', '.') }} ₫</h3>
            </div>
        </div>
    </div>

    <!-- Hàng 2: Danh Sách Sản Phẩm -->
    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-blue-50/50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-blue-800"><i class="fa-solid fa-list mr-2"></i>Danh Sách Sản Phẩm</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sản Phẩm</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nhà Xuất Bản</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Số Lượng</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Giá</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Trạng Thái</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <div class="flex items-center">
                                    @if($product->images->first())
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="Product" class="w-10 h-10 rounded object-cover mr-3">
                                    @else
                                        <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fa-solid fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    {{ $product->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->brand->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-center font-bold text-gray-800">{{ number_format($product->total_stock) }}</td>
                            <td class="px-6 py-4 text-sm text-center font-bold text-green-600">{{ number_format($product->price, 0, ',', '.') }} ₫</td>
                            <td class="px-6 py-4 text-sm text-center">
                                @if($product->status_color === 'green')
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">{{ $product->status }}</span>
                                @else
                                    <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold">{{ $product->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($product->status === 'Còn hàng')
                                    <button onclick="openAddToCartModal({{ $product->id }}, '{{ $product->name }}')" class="inline-block bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1 rounded text-xs font-bold transition">
                                        <i class="fa-solid fa-cart-plus mr-1"></i>Thêm
                                    </button>
                                @else
                                    <button disabled class="inline-block bg-gray-100 text-gray-400 px-3 py-1 rounded text-xs font-bold cursor-not-allowed">
                                        <i class="fa-solid fa-ban mr-1"></i>Hết hàng
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-3xl text-gray-300 mb-3 block"></i>
                                Không có sản phẩm nào có sẵn
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add to Cart Modal -->
<div id="addToCartModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 animate-fade-in">
        <h2 class="text-2xl font-black text-gray-800 mb-4">Thêm Vào Giỏ Hàng</h2>
        
        <form action="{{ route('customer.cart.add') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sản Phẩm</label>
                <p id="modalProductName" class="text-lg font-bold text-gray-800 bg-gray-50 px-4 py-3 rounded-lg"></p>
                <input type="hidden" name="product_id" id="modalProductId">
            </div>

            <div>
                <label for="modalQuantity" class="block text-sm font-bold text-gray-700 mb-2">Số Lượng</label>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="decreaseQuantity()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded font-bold transition">−</button>
                    <input type="number" name="quantity" id="modalQuantity" value="1" min="1" max="1000" class="flex-1 px-4 py-2 border-2 border-blue-500 rounded-lg text-center text-lg font-bold focus:outline-none" required>
                    <button type="button" onclick="increaseQuantity()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded font-bold transition">+</button>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeAddToCartModal()" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                    Hủy
                </button>
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    <i class="fa-solid fa-check mr-2"></i>Thêm Vào Giỏ
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
    #addToCartModal:not(.hidden) {
        animation: fadeIn 0.2s ease-out;
    }
</style>

<script>
    function openAddToCartModal(productId, productName) {
        const modal = document.getElementById('addToCartModal');
        document.getElementById('modalProductId').value = productId;
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalQuantity').value = 1;
        modal.classList.remove('hidden');
    }

    function closeAddToCartModal() {
        const modal = document.getElementById('addToCartModal');
        modal.classList.add('hidden');
    }

    function increaseQuantity() {
        const input = document.getElementById('modalQuantity');
        input.value = Math.min(parseInt(input.value) + 1, 1000);
    }

    function decreaseQuantity() {
        const input = document.getElementById('modalQuantity');
        input.value = Math.max(parseInt(input.value) - 1, 1);
    }

    // Close modal when clicking outside
    document.getElementById('addToCartModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddToCartModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddToCartModal();
        }
    });
</script>

@endsection
