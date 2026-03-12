@props([
    'variant' => 'primary', 
    'href' => null,       
])

@php
    $baseClasses = 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring disabled:opacity-25 transition ease-in-out duration-150';
    
    $variants = [
        'primary' => 'bg-blue-600 border-transparent text-white hover:bg-blue-700 active:bg-blue-900 focus:border-blue-900 focus:ring-blue-300',
        'secondary' => 'bg-gray-200 border-transparent text-gray-700 hover:bg-gray-300 active:bg-gray-400 focus:border-gray-500 focus:ring-gray-200',
        'danger' => 'bg-red-600 border-transparent text-white hover:bg-red-700 active:bg-red-900 focus:border-red-900 focus:ring-red-300',
        'success' => 'bg-green-600 border-transparent text-white hover:bg-green-700 active:bg-green-900 focus:border-green-900 focus:ring-green-300',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif