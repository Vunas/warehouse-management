<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Customer Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <style>
        .nav-link.active {
            background-color: #1e293b;
            color: white;
            border-left: 4px solid #3b82f6;
        }
        .nav-link {
            border-left: 4px solid transparent;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans text-gray-800">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col ml-64 min-w-0">

        {{-- Header --}}
        @include('partials.header')

        <main class="flex-1 overflow-y-auto bg-gray-50 p-6">

            {{-- Flash message --}}
            @include('partials.flash')

            {{-- Page content --}}
            @yield('content')
        </main>

    </div>
</div>

</body>
</html>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
        class="bg-red-500 text-white px-4 py-2 rounded">
        Đăng xuất
    </button>
</form>
