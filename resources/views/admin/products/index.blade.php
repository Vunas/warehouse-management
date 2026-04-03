@extends('layouts.admin')

@section('content')
    @php
        $createRoute = auth()->user()->can('create', App\Models\Product::class) 
            ? route('products.create') 
            : null;
    @endphp

    <x-crud.index 
        title="Quản lý Sản phẩm" 
        :createRoute="$createRoute" 
        :data="$products"
    >
        <x-slot name="filters">
            <x-ui.filter-bar action="{{ route('products.index') }}">
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm mã, tên sản phẩm..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <select name="status" class="block w-full md:w-40 py-2 pl-3 pr-10 border border-gray-300 rounded-lg sm:text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang kinh doanh</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                </select>

                <x-slot name="perPage">
                    <div class="flex items-center text-sm text-gray-500">
                        <span class="mr-2">Hiển thị:</span>
                        <select name="per_page" onchange="this.form.submit()"
                            class="py-1.5 pl-3 pr-8 border border-gray-300 rounded-lg text-sm">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </x-slot>
            </x-ui.filter-bar>
        </x-slot>

        <x-ui.table>
            <x-slot name="header">
                <x-ui.table.column name="id">Mã SP</x-ui.table.column>
                <x-ui.table.column name="name">Thông tin sản phẩm</x-ui.table.column>
                <x-ui.table.column name="price">Giá bán (VNĐ)</x-ui.table.column>
                <x-ui.table.column name="is_active" align="center">Trạng thái</x-ui.table.column>
                <x-ui.table.column align="right">Thao tác</x-ui.table.column>
            </x-slot>

            @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">PRD-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</td>
                    
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 shrink-0 bg-gray-100 rounded flex items-center justify-center">
                                <i class="fa-solid fa-box text-gray-400"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">
                                    Danh mục: {{ $product->category->name ?? 'N/A' }} | Thương hiệu: {{ $product->brand->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                        {{ number_format($product->price, 0, ',', '.') }} đ
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($product->is_active)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Đang kinh doanh</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Ngừng kinh doanh</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            @can('update', $product)
                                <a href="{{ route('products.edit', $product->id) }}" class="text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-md hover:bg-indigo-100">Sửa</a>
                            @endcan
                            @can('delete', $product)
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 bg-red-50 px-3 py-1.5 rounded-md hover:bg-red-100">Xóa</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">Không có sản phẩm nào</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-crud.index>
@endsection