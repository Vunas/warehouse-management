@extends('layouts.admin')

@php
    $isEdit = isset($warehouse);
    $action = $isEdit ? route('warehouses.update', $warehouse->id) : route('warehouses.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Cập nhật Kho bãi' : 'Thêm Kho bãi mới';
@endphp

@section('content')
<x-crud.form :title="$title" :action="$action" :method="$method" cancelRoute="{{ route('warehouses.index') }}">
    <div class="md:col-span-2 space-y-6">
        <x-ui.input name="name" label="Tên Kho" :value="old('name', $warehouse->name ?? '')" required="true" />
        
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Địa chỉ / Vị trí chi tiết *</label>
            <textarea name="location" rows="3" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('location', $warehouse->location ?? '') }}</textarea>
            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</x-crud.form>
@endsection