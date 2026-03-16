@props([
    'sortable' => false,
    'name' => '', // Tên column trong DB để sort
    'align' => 'left' // left, center, right
])

@php
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];
    $alignClass = $alignClasses[$align] ?? 'text-left';

    // Logic xử lý URL sắp xếp
    $currentSort = request('sort');
    $currentDir = request('dir', 'desc');
    
    $isSortingThis = $currentSort === $name;
    $nextDir = ($isSortingThis && $currentDir === 'asc') ? 'desc' : 'asc';
    
    // fullUrlWithQuery giúp giữ nguyên các biến ?search=..&status=.. khi ta bấm sort
    $sortUrl = $sortable ? request()->fullUrlWithQuery(['sort' => $name, 'dir' => $nextDir]) : '#';
@endphp

<th scope="col" class="px-6 py-4 {{ $alignClass }} text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
    @if($sortable)
        <a href="{{ $sortUrl }}" class="group inline-flex items-center space-x-1 hover:text-gray-900 transition-colors">
            <span>{{ $slot }}</span>
            <span class="ml-2 flex-none rounded bg-gray-100 text-gray-900 group-hover:bg-gray-200">
                @if($isSortingThis)
                    @if($currentDir === 'asc')
                        <!-- Icon Sort Asc -->
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                    @else
                        <!-- Icon Sort Desc -->
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    @endif
                @else
                    <!-- Icon Sort Default (Ẩn mờ, hiện khi hover) -->
                    <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                @endif
            </span>
        </a>
    @else
        <span>{{ $slot }}</span>
    @endif
</th>