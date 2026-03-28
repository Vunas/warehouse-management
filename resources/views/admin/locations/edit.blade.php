@extends('layouts.admin')

@php
    $isEdit = true;
    $action = route('locations.update', $location->id);
    $method = 'PUT';
@endphp

@section('content')
    <x-crud.form title="Sửa Vị trí" :action="$action" :method="$method" cancelRoute="{{ route('locations.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full md:col-span-2">
            <input type="hidden" name="warehouse_id" value="{{ $location->warehouse_id }}">

            <x-ui.input name="name" label="Tên vị trí *" :value="old('name', $location->name)" required="true" />

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Loại vị trí *</label>
                <select name="type" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                    <option value="zone" {{ $location->type == 'zone' ? 'selected' : '' }}>Zone</option>
                    <option value="rack" {{ $location->type == 'rack' ? 'selected' : '' }}>Rack</option>
                    <option value="shelf" {{ $location->type == 'shelf' ? 'selected' : '' }}>Shelf</option>
                    <option value="pallet" {{ $location->type == 'pallet' ? 'selected' : '' }}>Pallet</option>
                    <option value="bin" {{ $location->type == 'bin' ? 'selected' : '' }}>Bin</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Vị trí cha</label>
                <select name="parent_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                    <option value="">[Trống] -- Trực thuộc Kho tổng</option>
                    @foreach($flatLocations as $loc)
                        <option value="{{ $loc->id }}" {{ $location->parent_id == $loc->id ? 'selected' : '' }}>
                            {{ str_repeat('-- ', $loc->level ?? 0) }}{{ $loc->name }} ({{ $loc->type }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-start bg-indigo-50 p-3 rounded-lg border border-indigo-100 mt-2 md:col-span-2">
                <input id="is_store" name="is_store" type="checkbox" value="1" {{ $location->is_store ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                <label for="is_store" class="ml-3 text-sm font-bold text-gray-800 cursor-pointer">Cho phép lưu trữ hàng</label>
            </div>
        </div>
    </x-crud.form>
@endsection
