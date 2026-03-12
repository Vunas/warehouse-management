@props(['data'])

@if($data->hasPages())
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Hiển thị <span class="font-medium">{{ $data->firstItem() }}</span> đến <span class="font-medium">{{ $data->lastItem() }}</span> 
                    trong tổng số <span class="font-medium">{{ $data->total() }}</span> kết quả
                </p>
            </div>
            <div>
                {{ $data->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endif