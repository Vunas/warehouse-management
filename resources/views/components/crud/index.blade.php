@props([
    'title',
    'createRoute' => null,
    'createLabel' => 'Thêm mới',
    'data' => null,
    'filters' => null, // Slot chứa các form filter/search
])

<div class="p-4 sm:p-6 lg:p-8 w-full max-w-9xl mx-auto">
    <!-- Header Section -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $title }}</h1>
            <p class="mt-2 text-sm text-gray-600">Quản lý và cập nhật danh sách dữ liệu hệ thống.</p>
        </div>
        
        @if($createRoute)
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <a href="{{ $createRoute }}" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ $createLabel }}
                </a>
            </div>
        @endif
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200 shadow-sm transition-all duration-300">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-200 overflow-hidden">
        
        <!-- Toolbar (Filters & Search) -->
        @if($filters)
            <div class="bg-gray-50/50 border-b border-gray-200 px-6 py-4">
                <form method="GET" action="{{ url()->current() }}" class="flex flex-col sm:flex-row flex-wrap gap-4 items-center justify-between">
                    {{ $filters }}
                </form>
            </div>
        @endif

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    {{ $slot }}
                </table>
            </div>
        </div>

        <!-- Pagination & Footer -->
        @if($data && method_exists($data, 'links'))
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Hiển thị từ <span class="font-medium text-gray-900">{{ $data->firstItem() ?? 0 }}</span> đến <span class="font-medium text-gray-900">{{ $data->lastItem() ?? 0 }}</span> trong số <span class="font-medium text-gray-900">{{ $data->total() }}</span> kết quả
                    </div>
                    <div>
                        {{ $data->onEachSide(1)->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>