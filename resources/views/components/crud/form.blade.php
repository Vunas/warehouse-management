@props([
    'title',
    'action',
    'method' => 'POST',
    'cancelRoute',
])

<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ $title }}
            </h3>
        </div>

        <!-- Form -->
        <form action="{{ $action }}" method="POST">
            @csrf
            @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method($method)
            @endif

            <div class="px-6 py-4">
                <!-- Nội dung các input sẽ được chèn vào đây -->
                {{ $slot }}
            </div>

            <!-- Footer (Buttons) -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <x-ui.button href="{{ $cancelRoute }}" variant="secondary">
                    Hủy bỏ
                </x-ui.button>
                <x-ui.button type="submit" variant="success">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Lưu dữ liệu
                </x-ui.button>
            </div>
        </form>
    </div>
</div>