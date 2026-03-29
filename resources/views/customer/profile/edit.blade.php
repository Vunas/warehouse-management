@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Tiêu đề -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-800">Quản Lý Tài Khoản</h1>
        <p class="text-gray-600 mt-2">Xem và chỉnh sửa thông tin tài khoản của bạn</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sidebar: Menu -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-blue-50 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-blue-800"><i class="fa-solid fa-sliders mr-2"></i>Cài Đặt</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    <a href="#profile-section" class="block px-6 py-3 hover:bg-blue-50 text-gray-700 hover:text-blue-600 font-medium transition">
                        <i class="fa-solid fa-user mr-2"></i>Thông Tin Cá Nhân
                    </a>
                    <a href="#password-section" class="block px-6 py-3 hover:bg-blue-50 text-gray-700 hover:text-blue-600 font-medium transition">
                        <i class="fa-solid fa-lock mr-2"></i>Mật Khẩu
                    </a>
                    <a href="#delete-section" class="block px-6 py-3 hover:bg-red-50 text-gray-700 hover:text-red-600 font-medium transition">
                        <i class="fa-solid fa-trash mr-2"></i>Xóa Tài Khoản
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Display Messages -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800 font-bold mb-2"><i class="fa-solid fa-exclamation-circle mr-2"></i>Có lỗi xảy ra:</p>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center">
                    <i class="fa-solid fa-check-circle text-green-600 mr-3 text-xl"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Profile Information Section -->
            <div id="profile-section" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-blue-50 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-blue-800"><i class="fa-solid fa-user mr-2"></i>Thông Tin Cá Nhân</h2>
                </div>
                
                <div class="p-6">
                    <!-- Current Info Display -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 pb-8 border-b border-gray-100">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-bold text-gray-500 uppercase">Tên Đăng Nhập</p>
                            <p class="text-lg font-bold text-gray-800 mt-1">{{ $user->username }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-bold text-gray-500 uppercase">Email</p>
                            <p class="text-lg font-bold text-gray-800 mt-1">{{ $user->email }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-bold text-gray-500 uppercase">Tên Đầy Đủ</p>
                            <p class="text-lg font-bold text-gray-800 mt-1">{{ $user->full_name }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-bold text-gray-500 uppercase">Số Điện Thoại</p>
                            <p class="text-lg font-bold text-gray-800 mt-1">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="username" class="block text-sm font-bold text-gray-700 mb-2">Tên Đăng Nhập</label>
                            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label for="full_name" class="block text-sm font-bold text-gray-700 mb-2">Tên Đầy Đủ</label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->full_name) }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Số Điện Thoại</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                placeholder="0xxxxxxxxx" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                                <i class="fa-solid fa-save mr-2"></i>Lưu Thay Đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Section -->
            <div id="password-section" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-yellow-50 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-yellow-800"><i class="fa-solid fa-lock mr-2"></i>Đổi Mật Khẩu</h2>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('customer.profile.updatePassword') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-bold text-gray-700 mb-2">Mật Khẩu Hiện Tại</label>
                            <input type="password" name="current_password" id="current_password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                            <p class="text-xs text-gray-500 mt-1">Nhập mật khẩu hiện tại để xác nhận danh tính</p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Mật Khẩu Mới</label>
                            <input type="password" name="password" id="password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                            <p class="text-xs text-gray-500 mt-1">Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm số và ký tự đặc biệt</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">Xác Nhận Mật Khẩu Mới</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-yellow-700 transition">
                                <i class="fa-solid fa-key mr-2"></i>Cập Nhật Mật Khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Account Section -->
            <div id="delete-section" class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
                <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                    <h2 class="text-lg font-bold text-red-800"><i class="fa-solid fa-trash mr-2"></i>Xóa Tài Khoản</h2>
                </div>
                
                <div class="p-6">
                    <p class="text-gray-700 mb-4">
                        <i class="fa-solid fa-exclamation-triangle text-red-600 mr-2"></i>
                        <strong>Cảnh báo:</strong> Xóa tài khoản là hành động không thể hoàn tác. Tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn.
                    </p>

                    <form id="deleteAccountForm" action="{{ route('customer.profile.delete') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label for="delete_password" class="block text-sm font-bold text-gray-700 mb-2">Nhập Mật Khẩu để Xác Nhận</label>
                            <input type="password" name="password" id="delete_password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
                        </div>

                        <div class="pt-4">
                            <button type="button" onclick="confirmDelete()" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 transition">
                                <i class="fa-solid fa-times-circle mr-2"></i>Xóa Tài Khoản Vĩnh Viễn
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm('Bạn đã hiểu rõ điều này sẽ xóa tài khoản của bạn vĩnh viễn? Tất cả dữ liệu sẽ bị xóa và không thể khôi phục.')) {
            if (confirm('Vui lòng xác nhận lần nữa - hành động này không thể hoàn tác!')) {
                document.getElementById('deleteAccountForm').submit();
            }
        }
    }
</script>
@endsection
