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

        <!-- Phần lọc và tìm kiếm -->
        <form method="GET" action="{{ route('customer.dashboard') }}"
            class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
            <div class="flex items-end gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tìm kiếm sản phẩm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nhập tên sản phẩm..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium flex items-center gap-2">
                        <i class="fa-solid fa-search"></i>
                        <span>Tìm kiếm</span>
                    </button>
                    <button type="button" onclick="toggleAdvancedSearch(this)"
                        class="px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors font-medium flex items-center gap-2">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Nâng cao</span>
                    </button>
                    @if (request('search') ||
                            request('category_id') ||
                            request('brand_id') ||
                            request('price_from') ||
                            request('price_to') ||
                            request('stock_status'))
                        <a href="{{ route('customer.dashboard') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-medium flex items-center gap-2">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Xóa</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Form Tìm kiếm nâng cao - Ẩn theo mặc định -->
            <div id="advancedSearchForm" class="hidden border-t border-gray-200 pt-4 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Danh mục -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                        <select name="category_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Thương hiệu -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                        <select name="brand_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Tất cả thương hiệu --</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Trạng thái hàng -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                        <select name="stock_status"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>Còn
                                hàng</option>
                            <option value="out_of_stock"
                                {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                        </select>
                    </div>

                    <!-- Giá từ -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Giá từ (VNĐ)</label>
                        <input type="number" name="price_from" value="{{ request('price_from') }}" placeholder="0"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Giá đến -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Giá đến (VNĐ)</label>
                        <input type="number" name="price_to" value="{{ request('price_to') }}"
                            placeholder="Không giới hạn"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <!-- Nút tìm kiếm và reset -->
                <div class="mt-4 flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium flex items-center gap-2">
                        <i class="fa-solid fa-search"></i>
                        <span>Tìm kiếm</span>
                    </button>
                    <a href="{{ route('customer.dashboard') }}"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-medium flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Xóa bộ lọc</span>
                    </a>
                </div>
            </div>

            <script>
                function toggleAdvancedSearch(button) {
                    const form = document.getElementById('advancedSearchForm');
                    const isHidden = form.classList.contains('hidden');

                    if (isHidden) {
                        form.classList.remove('hidden');
                        button.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                        button.classList.remove('bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
                    } else {
                        form.classList.add('hidden');
                        button.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                        button.classList.add('bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
                    }
                }

                // Tự động mở form tìm kiếm nâng cao nếu có bất kỳ bộ lọc nâng cao nào
                window.addEventListener('DOMContentLoaded', function() {
                    @if (request('category_id') ||
                            request('brand_id') ||
                            request('stock_status') ||
                            request('price_from') ||
                            request('price_to'))
                        document.getElementById('advancedSearchForm').classList.remove('hidden');
                        const btn = document.querySelector('button[onclick="toggleAdvancedSearch(this)"]');
                        btn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                        btn.classList.remove('bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
                    @endif
                });
            </script>
        </form>

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
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Thương hiệu</th>
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
                                        <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fa-solid fa-image text-gray-400"></i>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $product->brand->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-center font-bold text-gray-800">
                                    {{ number_format($product->total_stock) }}</td>
                                <td class="px-6 py-4 text-sm text-center font-bold text-green-600">
                                    {{ number_format($product->price, 0, ',', '.') }} ₫</td>
                                <td class="px-6 py-4 text-sm text-center">
                                    @if ($product->status_color === 'green')
                                        <span
                                            class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-lg text-xs font-bold">{{ $product->status }}</span>
                                    @else
                                        <span
                                            class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-lg text-xs font-bold">{{ $product->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($product->status === 'Còn hàng')
                                        <button onclick="openAddToCartModal({{ $product->id }}, '{{ $product->name }}')"
                                            class="inline-block bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1 rounded text-xs font-bold transition">
                                            <i class="fa-solid fa-cart-plus mr-1"></i>Thêm
                                        </button>
                                    @else
                                        <button disabled
                                            class="inline-block bg-gray-100 text-gray-400 px-3 py-1 rounded text-xs font-bold cursor-not-allowed">
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
    <div id="addToCartModal" class="fixed inset-0 z-9999 hidden items-center justify-center p-4">

        <!-- Backdrop -->
        <div id="modalBackdrop"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm opacity-0 transition-all duration-300">
        </div>

        <!-- Modal -->
        <div id="modalPanel"
            class="relative w-full max-w-lg bg-white/95 backdrop-blur-xl rounded-3xl shadow-[0_25px_80px_rgba(0,0,0,.25)]
               border border-white/20
               opacity-0 scale-95 translate-y-6
               transition-all duration-300">

            <!-- Header -->
            <div class="relative p-6 border-b border-slate-100">

                <button type="button" onclick="closeAddToCartModal()"
                    class="absolute right-5 top-5 w-10 h-10 rounded-lg transition cursor-pointer">
                </button>

                <div class="flex items-center gap-4">
                    <div>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700">
                            Giỏ hàng
                        </span>

                        <h2 class="mt-2 text-xl font-black text-slate-900">
                            Thêm sản phẩm
                        </h2>

                        <p class="text-sm text-slate-500">
                            Chọn số lượng cần mua
                        </p>
                    </div>

                </div>
            </div>

            <form action="{{ route('customer.cart.add') }}" method="POST" class="p-6">

                @csrf

                <input type="hidden" name="product_id" id="modalProductId">
                <!-- Product -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">

                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-white shadow-sm flex items-center justify-center">
                            <i class="fa-solid fa-cube text-indigo-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Sản phẩm
                            </p>
                            <h3 id="modalProductName" class="font-bold text-slate-900 text-lg">
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="mt-6">

                    <label class="block text-sm font-bold text-slate-700 mb-4">
                        Số lượng
                    </label>

                    <div class="flex justify-center">

                        <div class="inline-flex items-center gap-4 bg-slate-100 rounded-lg px-4 py-3">

                            <button type="button" onclick="decreaseQuantity()"
                                class="w-12 h-12 rounded-lg cursor-pointer bg-white shadow hover:scale-110 hover:bg-indigo-600 hover:text-white transition-all duration-200">

                                <i class="fa-solid fa-minus"></i>

                            </button>

                            <input type="number" id="modalQuantity" name="quantity" value="1" min="1"
                                max="1000"
                                class="w-20 text-center text-2xl font-black bg-transparent border-none focus:ring-0"
                                required>

                            <button type="button" onclick="increaseQuantity()"
                                class="w-12 h-12 rounded-lg cursor-pointer bg-white shadow hover:scale-110 hover:bg-indigo-600 hover:text-white transition-all duration-200">

                                <i class="fa-solid fa-plus"></i>

                            </button>

                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="mt-8 flex gap-3">

                    <button type="button" onclick="closeAddToCartModal()"
                        class="flex-1 py-3 rounded-2xl border border-slate-300 font-bold hover:bg-slate-100 transition cursor-pointer">
                        Hủy
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-2xl border border-slate-300 font-bold hover:bg-slate-100 transition cursor-pointer">
                        Thêm vào giỏ
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
            const panel = document.getElementById('modalPanel');
            const backdrop = document.getElementById('modalBackdrop');

            document.getElementById('modalProductId').value = productId;
            document.getElementById('modalProductName').textContent = productName;
            document.getElementById('modalQuantity').value = 1;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            requestAnimationFrame(() => {

                backdrop.classList.remove('opacity-0');

                panel.classList.remove(
                    'opacity-0',
                    'scale-95',
                    'translate-y-6'
                );
            });
        }

        function closeAddToCartModal() {

            const modal = document.getElementById('addToCartModal');
            const panel = document.getElementById('modalPanel');
            const backdrop = document.getElementById('modalBackdrop');

            backdrop.classList.add('opacity-0');

            panel.classList.add(
                'opacity-0',
                'scale-95',
                'translate-y-6'
            );

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        function increaseQuantity() {

            const input = document.getElementById('modalQuantity');

            input.value = Math.min(
                parseInt(input.value || 1) + 1,
                1000
            );
        }

        function decreaseQuantity() {

            const input = document.getElementById('modalQuantity');

            input.value = Math.max(
                parseInt(input.value || 1) - 1,
                1
            );
        }

        document.addEventListener('keydown', function(e) {

            if (e.key === 'Escape') {
                closeAddToCartModal();
            }
        });

        document.getElementById('modalBackdrop')
            ?.addEventListener('click', closeAddToCartModal);
    </script>
@endsection
