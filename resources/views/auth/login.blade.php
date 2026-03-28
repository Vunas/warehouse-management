<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <!-- Thêm Tailwind CSS (Có thể thay bằng file CSS đã build của dự án) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Đăng Nhập</h2>
            <p class="text-gray-500 text-sm mt-2">Hệ thống Quản lý Kho</p>
        </div>

        <!-- Hiển thị thông báo thành công (nếu có lúc logout) -->
        @if (session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 text-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email đăng nhập</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full px-4 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                    placeholder="admin@example.com">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mật khẩu -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                    placeholder="••••••••">
            </div>

            <!-- Ghi nhớ & Quên mật khẩu -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900"> Ghi nhớ đăng nhập </label>
                </div>
                
                <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500"> Quên mật khẩu? </a>
                </div>
            </div>

            <!-- Nút Submit -->
            <div>
                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Đăng Nhập
                </button>
            </div>
        </form>
    </div>

</body>
</html>