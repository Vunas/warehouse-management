<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
        class="bg-red-500 text-white px-4 py-2 rounded">
        Đăng xuất
    </button>
</form>
