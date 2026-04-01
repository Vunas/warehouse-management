@props(['action' => url()->current()])

<form method="GET" action="{{ $action }}" class="bg-gray-50/50 border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row flex-wrap gap-4 items-center justify-between">
    <!-- Nơi chứa Search và Filters (Trái) -->
    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
        {{ $slot }}
        
        <div class="flex items-center gap-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                Lọc dữ liệu
            </button>
            @if(request()->anyFilled(['search', 'status', 'sort', 'role', 'include_inactive']))
                <a href="{{ $action }}" class="text-sm font-medium text-gray-500 hover:text-red-600 underline underline-offset-2 transition-colors">Xóa lọc</a>
            @endif
        </div>
    </div>

    <!-- Nơi chứa Per Page (Phải) -->
    @if(isset($perPage))
        {{ $perPage }}
    @endif
</form>