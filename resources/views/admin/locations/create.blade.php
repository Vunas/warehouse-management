@extends('layouts.admin')

@php
    $isEdit = false;
    $action = route('locations.store');
    $method = 'POST';
@endphp

@section('content')
    <x-crud.form title="Thêm Vị trí mới" :action="$action" :method="$method" cancelRoute="{{ route('locations.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full md:col-span-2">
            <input type="hidden" name="warehouse_id" value="{{ $warehouseId }}">

            <x-ui.input name="name" label="Tên vị trí *" :value="old('name')" required="true" />

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Loại vị trí *</label>
                <select name="type" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                    <option value="zone">Zone</option>
                    <option value="rack">Rack</option>
                    <option value="shelf">Shelf</option>
                    <option value="pallet">Pallet</option>
                    <option value="bin">Bin</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Vị trí cha</label>
                <select name="parent_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                    <option value="">[Trống] -- Trực thuộc Kho tổng</option>
                    @foreach($flatLocations as $loc)
                        <option value="{{ $loc->id }}">{{ str_repeat('-- ', $loc->level ?? 0) }}{{ $loc->name }} ({{ $loc->type }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-start bg-indigo-50 p-3 rounded-lg border border-indigo-100 mt-2 md:col-span-2">
                <input id="is_store" name="is_store" type="checkbox" value="1" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                <label for="is_store" class="ml-3 text-sm font-bold text-gray-800 cursor-pointer">Cho phép lưu trữ hàng</label>
            </div>
        </div>
    </x-crud.form>
@endsection
