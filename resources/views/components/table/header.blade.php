@props([
    'columns' => [], // Mảng dạng ['column_db' => 'Tên hiển thị'] hoặc ['column_db' => ['label' => 'Tên', 'sortable' => true]]
    'sortColumn' => 'created_at',
    'sortDirection' => 'desc',
])

<thead class="bg-gray-50">
    <tr>
        @foreach($columns as $key => $column)
            @php
                $label = is_array($column) ? $column['label'] : $column;
                $isSortable = is_array($column) && isset($column['sortable']) && $column['sortable'];
                
                $newDirection = ($key === $sortColumn && $sortDirection === 'asc') ? 'desc' : 'asc';
                $sortUrl = request()->fullUrlWithQuery(['sort' => $key, 'direction' => $newDirection]);
            @endphp

            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                @if($isSortable)
                    <a href="{{ $sortUrl }}" class="group inline-flex items-center space-x-1 hover:text-gray-700">
                        <span>{{ $label }}</span>
                        
                        {{-- Icon Sort --}}
                        <span class="ml-2 flex-none rounded text-gray-400 group-hover:visible group-focus:visible">
                            @if($key === $sortColumn)
                                @if($sortDirection === 'asc')
                                    <!-- Icon lên (Heroicons) -->
                                    <svg class="h-4 w-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                                @else
                                    <!-- Icon xuống -->
                                    <svg class="h-4 w-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                @endif
                            @else
                                <!-- Icon mặc định (mờ) -->
                                <svg class="h-4 w-4 invisible group-hover:visible" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                            @endif
                        </span>
                    </a>
                @else
                    <span>{{ $label }}</span>
                @endif
            </th>
        @endforeach
        
        {{-- Cột hành động (tùy chọn) --}}
        <th scope="col" class="relative px-6 py-3">
            <span class="sr-only">Actions</span>
        </th>
    </tr>
</thead>