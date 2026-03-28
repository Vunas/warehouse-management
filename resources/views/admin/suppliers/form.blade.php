@extends('layouts.admin')

@php
    $isEdit = isset($supplier);
    $action = $isEdit ? route('suppliers.update', $supplier->id) : route('suppliers.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

@section('content')
    <x-crud.form title="{{ $isEdit ? 'Sửa Nhà Cung Cấp' : 'Thêm Nhà Cung Cấp' }}" :action="$action" :method="$method" cancelRoute="{{ route('suppliers.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full md:col-span-2">
            <x-ui.input name="name" label="Tên Nhà Cung Cấp *" :value="old('name', $supplier->name ?? '')" required="true" />
            <x-ui.input name="phone" label="Số điện thoại" :value="old('phone', $supplier->phone ?? '')" />
            <x-ui.input name="email" type="email" label="Email" :value="old('email', $supplier->email ?? '')" />
            <x-ui.input name="address" label="Địa chỉ" :value="old('address', $supplier->address ?? '')" />
        </div>
    </x-crud.form>
@endsection