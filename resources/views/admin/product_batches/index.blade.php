@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Lô hàng (Batches)</h1>
                <p class="mt-2 text-sm text-gray-700">Danh sách các lô sản phẩm, ngày sản xuất và hạn sử dụng.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('product-batches.create') }}"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                    + Thêm Lô Mới
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white shadow  sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Mã Lô</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sản phẩm</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">NSX</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">HSD</th>
                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Trạng thái</th>
                        <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($batches as $batch)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                {{ $batch->batch_code }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ $batch->product->name ?? 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ $batch->manufacture_date ? $batch->manufacture_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ $batch->expiry_date ? $batch->expiry_date->format('d/m/Y') : '-' }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                @if ($batch->expiry_date)
                                    {{-- Hết hạn --}}
                                    @if ($batch->expiry_date->isPast())
                                        <span
                                            class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold leading-5 text-red-800">
                                            Hết hạn
                                        </span>

                                        {{-- Sắp hết hạn (<= 30 ngày) --}}
                                    @elseif(now()->diffInDays($batch->expiry_date, false) <= 30)
                                        <span
                                            class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold leading-5 text-yellow-800">
                                            Sắp hết hạn
                                        </span>

                                        {{-- Còn hạn --}}
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold leading-5 text-green-800">
                                            Còn hạn
                                        </span>
                                    @endif
                                @else
                                    {{-- Không có hạn sử dụng --}}
                                    <span
                                        class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold leading-5 text-gray-800">
                                        Không có HSD
                                    </span>
                                @endif
                            </td>

                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <a href="{{ route('product-batches.edit', $batch->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                <form action="{{ route('product-batches.destroy', $batch->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa lô hàng này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-gray-500">Chưa có dữ liệu lô hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-200">
                {{ $batches->links() }}
            </div>
        </div>
    </div>
@endsection
