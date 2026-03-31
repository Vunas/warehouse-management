<aside
    class="w-64 bg-slate-900 text-slate-300
         flex flex-col
         fixed inset-y-0 left-0
         z-20 shadow-xl">

    {{-- Brand --}}
    <div class="h-16 flex items-center justify-center border-b border-slate-800 bg-slate-950">
        <span class="text-xl font-bold text-white">
            <i class="fa-solid fa-boxes-stacked text-blue-500 mr-2"></i> WMS PRO
        </span>
    </div>

    <nav class="scrollbar-hidden  flex-1 overflow-y-auto py-4 px-0 space-y-1 ">

        <!-- Dashboard -->
        <a href="{{ route('customer.dashboard') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line w-6 text-center"></i>
            <span class="ml-2 font-medium">Tổng quan</span>
        </a>

        <!-- Module: Mua Sắm -->
        <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Mua Sắm</div>

        <a href="{{ route('customer.dashboard') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-shopping-bag w-6 text-center"></i>
            <span class="ml-2">Nhập Hàng</span>
        </a>

        <a href="{{ route('customer.cart.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors relative {{ request()->routeIs('customer.cart.*') ? 'active' : '' }}">
            <i class="fa-solid fa-cart-shopping w-6 text-center"></i>
            <span class="ml-2">Hàng Chờ Nhập</span>
            @php
                $cartCount = Auth::check() ? \App\Models\CartItem::where('user_id', Auth::id())->sum('quantity') : 0;
            @endphp
            @if($cartCount > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $cartCount }}</span>
            @endif
        </a>

        <a href="{{ route('customer.address.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('customer.address.*') ? 'active' : '' }}">
            <i class="fa-solid fa-map-location-dot w-6 text-center"></i>
            <span class="ml-2">Địa Chỉ Giao Hàng</span>
        </a>

        <!-- Module: Tài Khoản -->
        <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tài Khoản</div>

        <a href="{{ route('customer.profile.edit') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('customer.profile.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear w-6 text-center"></i>
            <span class="ml-2">Quản lý tài khoản</span>
        </a>

    </nav>

    {{-- User + logout --}}
    <div class="p-4 border-t border-slate-800 bg-slate-950">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs">
                {{ substr(Auth::user()->full_name ?? 'U', 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-sm font-medium text-white truncate">{{ Auth::user()->full_name ?? 'User' }}</div>
                <div class="text-xs text-slate-500 truncate">{{ Auth::user()->username ?? 'N/A' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('customer.logout') }}">
            @csrf
            <button type="submit" class="w-full text-red-400 hover:text-white transition">
                <i class="fa-solid fa-sign-out-alt mr-2"></i>Đăng xuất
            </button>
        </form>
    </div>
</aside>
