@extends('layouts.admin')

@php
    $isEdit = isset($inventory);
    $action = $isEdit ? route('inventory.update', $inventory->id) : route('inventory.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Cập nhật Tồn kho' : 'Thêm Tồn kho mới';
@endphp

@section('content')
    <x-crud.form 
        :title="$title" 
        :action="$action" 
        :method="$method" 
        cancelRoute="{{ route('inventory.index') }}"
    >
        <div class="space-y-6 md:col-span-2 lg:col-span-1">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sản phẩm *</label>
                <select name="product_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id', $inventory->product_id ?? '') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Vị trí (Location) *</label>
                <select name="location_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">-- Chọn vị trí --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id', $inventory->location_id ?? '') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->warehouse->name ?? '' }} - {{ $loc->name }} ({{ strtoupper($loc->type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <x-ui.input 
                name="quantity" 
                type="number" 
                label="Số lượng" 
                :value="old('quantity', $inventory->quantity ?? 0)" 
                required="true" 
            />
        </div>
    </x-crud.form>
@endsection