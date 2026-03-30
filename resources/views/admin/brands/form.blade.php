@extends('layouts.admin')

@php
    $isEdit = isset($brand);
    $action = $isEdit ? route('brands.update', $brand->id) : route('brands.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

@section('content')
    <x-crud.form title="{{ $isEdit ? 'Sửa Thương hiệu' : 'Thêm Thương hiệu' }}" :action="$action" :method="$method" cancelRoute="{{ route('brands.index') }}">
        <div class="md:col-span-2">
            <x-ui.input name="name" label="Tên Thương hiệu" :value="old('name', $brand->name ?? '')" required="true" />
        </div>
    </x-crud.form>
@endsection