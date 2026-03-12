@props([
    'title',
    'createRoute' => null,
    'createLabel' => 'Thêm mới',
    'data' => null // Truyền biến paginator vào đây
])

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
        
        @if($createRoute)
            <x-ui.button href="{{ $createRoute }}" variant="primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                {{ $createLabel }}
            </x-ui.button>
        @endif
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Content (Bảng dữ liệu) -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                {{ $slot }} <!-- Nội dung bảng sẽ được chèn vào đây -->
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($data && method_exists($data, 'links'))
        <div class="mt-6">
            {{ $data->links() }}
        </div>
    @endif
</div>