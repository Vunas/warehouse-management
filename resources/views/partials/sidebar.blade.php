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
        <a href="{{ route('admin.dashboard') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line w-6 text-center"></i>
            <span class="ml-2 font-medium">Tổng quan</span>
        </a>

        <!-- Module: Vận hành Kho -->
        <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Vận hành</div>

        <a href="{{ route('inbound_tickets.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('inbound_tickets.*') ? 'active' : '' }}">
            <i class="fa-solid fa-truck-ramp-box w-6 text-center"></i>
            <span class="ml-2">Nhập kho</span>
        </a>

        <a href="{{ route('outbound_tickets.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('outbound_tickets.*') ? 'active' : '' }}">
            <i class="fa-solid fa-dolly w-6 text-center"></i>
            <span class="ml-2">Xuất kho</span>
        </a>

        <a href="{{ route('transfers.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
            <i class="fa-solid fa-arrow-right-arrow-left w-6 text-center"></i>
            <span class="ml-2">Chuyển nội bộ</span>
        </a>

        <a href="{{ route('inventory.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <i class="fa-solid fa-cubes-stacked w-6 text-center"></i>
            <span class="ml-2">Tồn kho</span>
        </a>

        <!-- Module: Quản lý -->
        <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Quản lý</div>

        <a href="{{ route('contracts.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-contract w-6 text-center"></i>
            <span class="ml-2">Hợp đồng</span>
        </a>

        <a href="{{ route('customers.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="fa-solid fa-building-user w-6 text-center"></i>
            <span class="ml-2">Khách hàng</span>
        </a>

        <!-- Module: Cấu hình -->
        <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Cấu hình</div>

        <a href="{{ route('warehouses.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
            <i class="fa-solid fa-warehouse w-6 text-center"></i>
            <span class="ml-2">Kho & Lô</span>
        </a>

        <a href="{{ route('products.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tag w-6 text-center"></i>
            <span class="ml-2">Sản phẩm</span>
        </a>

        <!-- Module: Hệ thống (Admin Only) -->
        @can('viewAny', App\Models\Employee::class)
            <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Hệ thống</div>

            <a href="{{ route('employees.index') }}"
                class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear w-6 text-center"></i>
                <span class="ml-2">Nhân viên</span>
            </a>

            <a href="{{ route('roles.index') }}"
                class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved w-6 text-center"></i>
                <span class="ml-2">Phân quyền</span>
            </a>
        @endcan

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
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-red-400 hover:text-white">
                Đăng xuất
            </button>
        </form>
    </div>
</aside>
