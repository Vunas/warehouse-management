<ul class="pl-6 border-l border-gray-200 mt-2 space-y-2">
    @foreach($nodes as $node)
        <li class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white border border-gray-200 p-3 rounded-lg hover:border-indigo-300 hover:shadow-sm transition-all gap-3">
                <div class="flex items-center space-x-3">
                    <!-- Icon theo Type -->
                    <span class="px-2 py-1 text-xs font-bold rounded uppercase 
                        {{ $node->type == 'zone' ? 'bg-blue-100 text-blue-800' : 
                          ($node->type == 'rack' ? 'bg-purple-100 text-purple-800' : 
                          ($node->type == 'shelf' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ $node->type }}
                    </span>
                    <span class="font-semibold text-gray-800 text-sm">{{ $node->name }}</span>
                    @if($node->is_store)
                        <span class="ml-2 w-2 h-2 rounded-full bg-green-500" title="Vị trí chứa hàng (Có thể lưu trữ)"></span>
                    @endif
                </div>
                
                <div class="flex flex-wrap gap-2 justify-end items-center">
                    
                    <!-- Nút Thêm Con: Tự động điền Parent ID vào Form tạo mới bên cạnh -->
                    @if(!$node->is_store)
                        <button type="button" 
                                onclick="document.querySelector('select[name=parent_id]').value = '{{ $node->id }}'; document.querySelector('input[name=name]').focus(); window.scrollTo(0, 0);" 
                                class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded transition-colors shadow-sm" 
                                title="Thêm vị trí con trực thuộc">
                            + Thêm con
                        </button>
                    @endif

                    <!-- Các chức năng liên quan đến Hàng hóa (Chỉ hiện ở điểm chứa hàng) -->
                    @if($node->is_store)
                        <!-- Xem Tồn Kho tại vị trí này -->
                        <a href="{{ route('inventory.index', ['location_id' => $node->id]) }}" 
                           class="text-teal-600 hover:text-teal-800 text-xs font-medium px-2 py-1 bg-teal-50 hover:bg-teal-100 border border-teal-200 rounded transition-colors shadow-sm" 
                           title="Xem tồn kho tại vị trí này">
                            Tồn kho
                        </a>
                        
                        <!-- Tạo phiếu Chuyển Kho từ vị trí này -->
                        <a href="{{ route('transfers.create', ['from_location_id' => $node->id]) }}" 
                           class="text-orange-600 hover:text-orange-800 text-xs font-medium px-2 py-1 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded transition-colors shadow-sm" 
                           title="Tạo phiếu chuyển kho từ vị trí này">
                            Chuyển kho
                        </a>
                    @endif

                    <!-- Nút Sửa vị trí -->
                    <a href="{{ route('locations.edit', $node->id) }}" 
                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded transition-colors shadow-sm">
                        Sửa
                    </a>

                    <!-- Nút xóa (chỉ hiển thị nếu ko có vị trí con) -->
                    @if(count($node->children_tree) == 0)
                    <form action="{{ route('locations.destroy', $node->id) }}" method="POST" class="inline" onsubmit="return confirm('Chắc chắn xóa vị trí này?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 bg-red-50 hover:bg-red-100 border border-red-200 rounded transition-colors shadow-sm">Xóa</button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Gọi đệ quy nếu có con -->
            @if(count($node->children_tree) > 0)
                @include('admin.locations.partials.tree_node', ['nodes' => $node->children_tree])
            @endif
        </li>
    @endforeach
</ul>