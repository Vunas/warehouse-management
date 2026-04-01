<div class="bg-slate-900 text-slate-300 shadow-lg border-b border-slate-800">

    <div class="max-w-7xl mx-auto px-6">

        <div class="flex items-center justify-between h-16">

            {{-- LEFT: LOGO --}}
            <div class="flex items-center">
                <span class="text-xl font-bold text-white">
                    <i class="fa-solid fa-boxes-stacked text-blue-500 mr-2"></i> ALOVUA
                </span>
            </div>

            {{-- CENTER: MENU --}}
            <nav class="flex items-center gap-2">

                <!-- Tổng quan -->
                <a href="{{ route('customer.overview') }}"
                    class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('customer.overview') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line mr-2"></i>
                    Tổng quan
                </a>

                <!-- Nhập hàng -->
                <a href="{{ route('customer.dashboard') }}"
                    class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-shopping-bag mr-2"></i>
                    Nhập Hàng
                </a>

                <!-- Hàng chờ nhập -->
                <a href="{{ route('customer.cart.index') }}"
                    class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition relative {{ request()->routeIs('customer.cart.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping mr-2"></i>
                    Hàng Chờ Nhập

                    @php
                        $cartCount = Auth::check() ? \App\Models\CartItem::where('user_id', Auth::id())->sum('quantity') : 0;
                    @endphp

                    @if($cartCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <!-- Địa chỉ -->
                <a href="{{ route('customer.address.index') }}"
                    class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('customer.address.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-map-location-dot mr-2"></i>
                    Địa Chỉ
                </a>

                <!-- Tài khoản -->
                <a href="{{ route('customer.profile.edit') }}"
                    class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('customer.profile.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear mr-2"></i>
                    Tài khoản
                </a>

            </nav>

            {{-- RIGHT: USER --}}
            <div class="flex items-center gap-4">

                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ substr(Auth::user()->full_name ?? 'U', 0, 1) }}
                    </div>

                    <div class="hidden md:block">
                        <div class="text-sm font-medium text-white truncate">
                            {{ Auth::user()->full_name ?? 'User' }}
                        </div>
                        <div class="text-xs text-slate-400 truncate">
                            {{ Auth::user()->username ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-red-400 hover:text-white transition flex items-center">
                        <i class="fa-solid fa-sign-out-alt"></i>
                    </button>
                </form>

            </div>

        </div>

    </div>
</div>