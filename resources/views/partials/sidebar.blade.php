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
        <a href="{{ route('users.index') }}"
            class="nav-link flex items-center px-6 py-3 hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line w-6 text-center"></i>
            <span class="ml-2 font-medium">người dùng</span>
        </a>



    </nav>

    {{-- User + logout --}}
    {{-- <div class="p-4 border-t border-slate-800 bg-slate-950">
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
    </div> --}}
</aside>
