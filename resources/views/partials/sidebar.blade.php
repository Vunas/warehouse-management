@php
    $canViewUsers = auth()->user()->can('viewAny', App\Models\User::class);
    $canViewRoles = auth()->user()->can('viewAny', App\Models\Role::class);
    $canViewCategory = auth()->user()->can('viewAny', App\Models\Category::class);
    $canViewBrand = auth()->user()->can('viewAny', App\Models\Brand::class);
    $canViewSupplier = auth()->user()->can('viewAny', App\Models\Supplier::class);
    $canViewProduct = auth()->user()->can('viewAny', App\Models\Product::class);
    $canViewWarehouse = auth()->user()->can('viewAny', App\Models\Warehouse::class);
    $canViewLocation = auth()->user()->can('viewAny', App\Models\Location::class);
    $canViewInventory = auth()->user()->can('viewAny', App\Models\Inventory::class);
    $canViewInbound = auth()->user()->can('viewAny', App\Models\InboundOrder::class);
    $canViewOutbound = auth()->user()->can('viewAny', App\Models\OutboundOrder::class);
    $canViewTransfer = auth()->user()->can('viewAny', App\Models\StockTransfer::class);
    $canViewPayment = auth()->user()->can('viewAny', App\Models\Payment::class);
    $canViewOrder = auth()->user()->can('viewAny', App\Models\Order::class);
    $canViewCart = auth()->user()->can('viewAny', App\Models\CartItem::class);
@endphp

<aside class="w-64 bg-slate-900 text-slate-300 flex flex-col fixed inset-y-0 left-0 z-20 shadow-xl">

    {{-- Brand --}}
    <div class="h-16 flex items-center justify-center border-b border-slate-800 bg-slate-950">
        <span class="text-xl font-bold text-white">
            <i class="fa-solid fa-boxes-stacked text-blue-500 mr-2"></i> WMS PRO
        </span>
    </div>

    <nav class="scrollbar-hidden flex-1 overflow-y-auto py-4 px-0 space-y-1">

        {{-- Dashboard --}}
        @can('view_dashboard')
        <a href="{{ route('dashboard') }}"
            class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('dashboard') ? 'active bg-slate-800 text-white border-l-4 border-blue-500' : '' }}">
            <i class="fa-solid fa-chart-pie w-6 text-center"></i>
            <span class="ml-2 font-bold uppercase tracking-wider text-sm">Dashboard</span>
        </a>
        @endcan

        @if ($canViewUsers || $canViewRoles)
            {{-- Hệ thống --}}
            <div class="px-6 py-2 mt-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Hệ thống
            </div>
            @can('viewAny', App\Models\User::class)
                <a href="{{ route('users.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('users.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-users w-6 text-center"></i>
                    <span class="ml-2 font-medium">Người dùng</span>
                </a>
            @endcan

            @can('viewAny', App\Models\Role::class)
                <a href="{{ route('roles.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('roles.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-shield-halved w-6 text-center"></i>
                    <span class="ml-2 font-medium">Phân quyền</span>
                </a>
            @endcan
        @endif

        @if ($canViewCategory || $canViewBrand || $canViewSupplier || $canViewProduct || $canViewWarehouse || $canViewLocation)
            {{-- Master Data --}}
            <div class="px-6 py-2 mt-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Danh mục (Master Data)
            </div>
            @can('viewAny', App\Models\Category::class)
                <a href="{{ route('categories.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('categories.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-tags w-6 text-center"></i>
                    <span class="ml-2 font-medium">Danh mục SP</span>
                </a>
            @endcan

            @can('viewAny', App\Models\Brand::class)
                <a href="{{ route('brands.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('brands.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-copyright w-6 text-center"></i>
                    <span class="ml-2 font-medium">Thương hiệu</span>
                </a>
            @endcan

            @can('viewAny', App\Models\Supplier::class)
                <a href="{{ route('suppliers.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('suppliers.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-truck-field w-6 text-center"></i>
                    <span class="ml-2 font-medium">Nhà cung cấp</span>
                </a>
            @endcan

            @can('viewAny', App\Models\Product::class)
                <a href="{{ route('products.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('products.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-box-open w-6 text-center"></i>
                    <span class="ml-2 font-medium">Sản phẩm</span>
                </a>
            @endcan

            @can('viewAny', App\Models\Warehouse::class)
                <a href="{{ route('warehouses.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('warehouses.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-warehouse w-6 text-center"></i>
                    <span class="ml-2 font-medium">Kho bãi</span>
                </a>
            @endcan

            @can('viewAny', App\Models\Warehouse::class)
                <a href="{{ route('locations.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('locations.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-location-dot w-6 text-center"></i>
                    <span class="ml-2 font-medium">Vị trí kho</span>
                </a>
            @endcan
        @endif

        @if ($canViewInventory || $canViewInbound || $canViewOutbound || $canViewTransfer)
            {{-- Nghiệp vụ kho & bán hàng --}}
            <div class="px-6 py-2 mt-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Nghiệp vụ kho
            </div>
            @can('viewAny', App\Models\ProductAlert::class)
                <a href="{{ route('product_alerts.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('product_alerts.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-boxes-stacked w-6 text-center"></i>
                    <span class="ml-2 font-medium">Cảnh báo tồn kho</span>
                </a>
            @endcan

            @can('viewAny', App\Models\Inventory::class)
                <a href="{{ route('inventory.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('inventory.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-boxes-stacked w-6 text-center"></i>
                    <span class="ml-2 font-medium">Tồn kho</span>
                </a>
            @endcan

            @can('viewAny', App\Models\InboundOrder::class)
                <a href="{{ route('inbounds.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('inbounds.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-truck-arrow-right w-6 text-center"></i>
                    <span class="ml-2 font-medium">Nhập kho</span>
                </a>
            @endcan

            @can('viewAny', App\Models\OutboundOrder::class)
                <a href="{{ route('outbounds.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('outbounds.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-truck-fast w-6 text-center"></i>
                    <span class="ml-2 font-medium">Xuất kho</span>
                </a>
            @endcan

            @can('viewAny', App\Models\StockTransfer::class)
                <a href="{{ route('transfers.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('transfers.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-right-left w-6 text-center"></i>
                    <span class="ml-2 font-medium">Luân chuyển kho</span>
                </a>
            @endcan

            @can('viewAny', App\Models\StockTake::class)
                <a href="{{ route('stock_takes.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('stock_takes.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-box-open w-6 text-center"></i>
                    <span class="ml-2 font-medium">Kiểm kê kho</span>
                </a>
            @endcan

            @can('viewAny', App\Models\InventoryTransaction::class)
                <a href="{{ route('inventory_transactions.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('inventory_transactions.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-box-open w-6 text-center"></i>
                    <span class="ml-2 font-medium">Lịch sử giao dịch</span>
                </a>
            @endcan
        @endif

        @if ($canViewPayment || $canViewOrder || $canViewCart)
            {{-- Tài chính & Kinh doanh --}}
            <div class="px-6 py-2 mt-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Kinh doanh
            </div>

            @can('viewAny', App\Models\Payment::class)
                <a href="{{ route('payments.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('payments.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-money-bill-wave w-6 text-center"></i>
                    <span class="ml-2 font-medium">Thanh toán</span>
                </a>
            @endcan

            @can('viewAny', App\Models\Order::class)
                <a href="{{ route('orders.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('orders.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-money-bill-wave w-6 text-center"></i>
                    <span class="ml-2 font-medium">Đơn hàng</span>
                </a>
            @endcan

            @can('viewAny', App\Models\CartItem::class)
                <a href="{{ route('carts.index') }}"
                    class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('carts.*') ? 'active bg-slate-800 text-white' : '' }}">
                    <i class="fa-solid fa-cart-shopping w-6 text-center"></i>
                    <span class="ml-2 font-medium">Giỏ hàng rác</span>
                </a>
            @endcan

            @can('view_reports')
            <a href="{{ route('reports.index') }}"
                class="nav-link flex items-center px-6 py-3 {{ request()->routeIs('reports.index') ? 'active bg-slate-800 text-white border-l-4 border-blue-500' : '' }}">
                <i class="fa-solid fa-chart-pie w-6 text-center"></i>
                <span class="ml-2 font-bold uppercase tracking-wider text-sm">Báo cáo</span>
            </a>
            @endcan
        @endif
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
            <button type="submit" class="w-full text-red-400 hover:text-white text-left text-sm font-medium">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Đăng xuất
            </button>
        </form>
    </div>
</aside>
