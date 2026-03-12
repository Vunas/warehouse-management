@extends('layouts.admin')

@section('content')
    <!-- Gọi component crud.index -->
    <x-crud.index 
        title="Quản lý Người dùng" 
        createRoute="{{ route('users.create') }}" 
        :data="$users"
    >
        <!-- Khai báo Header của bảng -->
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tài khoản</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
            </tr>
        </thead>
        
        <!-- Khai báo Body của bảng -->
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <!-- Có thể viết thêm x-ui.badge cho đẹp -->
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Hoạt động' : 'Khóa' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                        <x-ui.button href="{{ route('users.edit', $user->id) }}" variant="secondary" class="px-2 py-1 text-xs">Sửa</x-ui.button>
                        
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn?');" class="inline">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="danger" class="px-2 py-1 text-xs">Xóa</x-ui.button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Chưa có dữ liệu.</td>
                </tr>
            @endforelse
        </tbody>
    </x-crud.index>
@endsection