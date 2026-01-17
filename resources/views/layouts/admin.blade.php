<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WMS Dashboard')</title>

    {{-- VITE (BẮT BUỘC) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <style>
        .nav-link.active {
            background-color: #1e293b;
            color: white;
            border-left: 4px solid #3b82f6;
        }
        .nav-link {
            border-left: 4px solid transparent;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col fixed h-full z-20 shadow-xl transition-all duration-300">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-center border-b border-slate-800 bg-slate-950">
                <span class="text-xl font-bold tracking-wider text-white">
                    <i class="fa-solid fa-boxes-stacked text-blue-500 mr-2"></i>WMS PRO
                </span>
            </div>

            <!-- Navigation -->
            <nav class="scrollbar-hidden  flex-1 overflow-y-auto py-4 px-0 space-y-1 scrollbar-hidden">
                
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line w-6 text-center"></i> 
                    <span class="ml-2 font-medium">Tổng quan</span>
                </a>

                <!-- Module: Vận hành Kho -->
                <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Vận hành</div>
                
                <a href="{{ route('inbound_tickets.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('inbound_tickets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-ramp-box w-6 text-center"></i>
                    <span class="ml-2">Nhập kho</span>
                </a>

                <a href="{{ route('outbound_tickets.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('outbound_tickets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-dolly w-6 text-center"></i>
                    <span class="ml-2">Xuất kho</span>
                </a>

                <a href="{{ route('transfers.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-arrow-right-arrow-left w-6 text-center"></i>
                    <span class="ml-2">Chuyển nội bộ</span>
                </a>

                <a href="{{ route('inventory.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cubes-stacked w-6 text-center"></i>
                    <span class="ml-2">Tồn kho</span>
                </a>

                <!-- Module: Quản lý -->
                <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Quản lý</div>

                <a href="{{ route('contracts.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-contract w-6 text-center"></i>
                    <span class="ml-2">Hợp đồng</span>
                </a>

                <a href="{{ route('customers.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building-user w-6 text-center"></i>
                    <span class="ml-2">Khách hàng</span>
                </a>

                <!-- Module: Cấu hình -->
                <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Cấu hình</div>

                <a href="{{ route('warehouses.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-warehouse w-6 text-center"></i>
                    <span class="ml-2">Kho & Lô</span>
                </a>

                <a href="{{ route('products.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tag w-6 text-center"></i>
                    <span class="ml-2">Sản phẩm</span>
                </a>

                <!-- Module: Hệ thống (Admin Only) -->
                @can('viewAny',App\Models\Employee::class)
                <div class="mt-6 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Hệ thống</div>
                
                <a href="{{ route('employees.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear w-6 text-center"></i>
                    <span class="ml-2">Nhân viên</span>
                </a>

                <a href="{{ route('roles.index') }}" class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-shield-halved w-6 text-center"></i>
                    <span class="ml-2">Phân quyền</span>
                </a>
                @endcan

            </nav>

            <!-- User Info & Logout -->
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
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center justify-center w-full px-4 py-2 text-sm text-red-400 bg-slate-900 rounded hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col ml-64 min-w-0 transition-all duration-300">
            <!-- Header -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10 shrink-0">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    @yield('header')
                </h2>
                
                <!-- Right Header Actions -->
                <div class="flex items-center space-x-4">
                    <button class="relative text-gray-400 hover:text-blue-600 transition">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 scroll-smooth">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center animate-fade-in-down">
                        <div class="flex items-center">
                            <i class="fa-solid fa-circle-check mr-2 text-xl"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center animate-fade-in-down">
                        <div class="flex items-center">
                            <i class="fa-solid fa-circle-exclamation mr-2 text-xl"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>