@extends('layouts.admin')

@php
    $isEdit = isset($category);
    $action = $isEdit ? route('categories.update', $category->id) : route('categories.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Cập nhật Danh mục' : 'Thêm Danh mục mới';
@endphp

@section('content')
<x-crud.form :title="$title" :action="$action" :method="$method" cancelRoute="{{ route('categories.index') }}">
    <div class="md:col-span-2 space-y-6">
        <x-ui.input name="name" label="Tên danh mục" :value="old('name', $category->name ?? '')" required="true" />
    </div>
</x-crud.form>
@endsection