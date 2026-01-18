@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')
@section('header', 'Danh sách Sản phẩm')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <form action="" method="GET" class="flex gap-2 relative">
            <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm SKU, tên..." class="pl-8 pr-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-blue-500 w-64">
        </form>
        @can('create', App\Models\Product::class)
        <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 shadow-sm">
            <i class="fa-solid fa-plus mr-1"></i> Thêm Sản phẩm
        </a>
        @endcan
    </div>

    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3">SKU</th>
                <th class="px-6 py-3">Tên sản phẩm</th>
                <th class="px-6 py-3">Danh mục</th>
                <th class="px-6 py-3">Mô tả</th>
                <th class="px-6 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-mono text-blue-600 font-bold">{{ $product->sku }}</td>
                <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                <td class="px-6 py-4">
                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                        {{ $product->category->name ?? 'N/A' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $product->description }}</td>
                <td class="px-6 py-4 text-right space-x-2">
                    @can('update', $product)
                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen"></i></a>
                    @endcan
                    @can('delete', $product)
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa sản phẩm này?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i></button>
                    </form>
                    @endcan
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Chưa có sản phẩm nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $products->links() }}</div>
</div>
@endsection