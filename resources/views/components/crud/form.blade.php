@props([
    'title',
    'action',
    'method' => 'POST',
    'cancelRoute',
    'description' => 'Vui lòng điền đầy đủ các thông tin bắt buộc bên dưới.'
])

<div class="p-4 sm:p-6 lg:p-8 w-full max-w-5xl mx-auto">
    
    <!-- Title & Breadcrumb -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ $title }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    </div>

    <div class="bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] rounded-xl border border-gray-200 overflow-hidden">
        <!-- Form -->
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method($method)
            @endif

            <div class="px-6 py-8">
                <!-- Nội dung các input sẽ được chèn vào đây. Hỗ trợ chia cột -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer (Buttons) -->
            <div class="bg-gray-50/80 px-6 py-5 border-t border-gray-200 flex items-center justify-end space-x-4">
                <a href="{{ $cancelRoute }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Lưu dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>