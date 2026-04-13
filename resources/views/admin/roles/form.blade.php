@extends('layouts.admin')

@php
    $isEdit = isset($role);
    $action = $isEdit ? route('roles.update', $role->id) : route('roles.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Cập nhật Vai trò' : 'Thêm Vai trò mới';

    // Gán biến array trống nếu tạo mới
    $rolePermissions = $rolePermissions ?? [];
    $oldPermissions = old('permissions', $rolePermissions);

    // Logic nhóm các quyền lại với nhau để hiển thị cho đẹp
    $permissionGroups = [
        'Hệ thống & Bảng điều khiển' => [
            'view_dashboard',
            'view_reports',
            'manage_roles',
            'view_alerts',
            'create_alerts',
            'edit_alerts',
            'delete_alerts',
        ],
        'Quản lý Người dùng' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
        'Sản phẩm & Danh mục' => [
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'manage_brands',
        ],
        'Lô hàng (Batches)' => [
            'view_product_batches',
            'create_product_batches',
            'edit_product_batches',
            'delete_product_batches',
        ],
        'Nhà cung cấp' => ['view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers'],
        'Kho bãi & Tồn kho' => [
            'view_warehouses',
            'create_warehouses',
            'edit_warehouses',
            'delete_warehouses',
            'view_inventory',
            'manage_inventory',
        ],
        'Giao dịch kho & Kiểm kê' => [
            'view_inventory_transactions',
            'create_inventory_transactions',
            'edit_inventory_transactions',
            'delete_inventory_transactions',
            'view_stock_takes',
            'create_stock_takes',
            'edit_stock_takes',
            'delete_stock_takes',
        ],
        'Phiếu Nhập Kho' => [
            'view_inbounds',
            'create_inbounds',
            'edit_inbounds',
            'approve_inbounds',
            'delete_inbounds',
        ],
        'Phiếu Xuất Kho' => [
            'view_outbounds',
            'create_outbounds',
            'edit_outbounds',
            'approve_outbounds',
            'delete_outbounds',
        ],
        'Điều chuyển kho' => [
            'view_transfers',
            'create_transfers',
            'edit_transfers',
            'approve_transfers',
            'delete_transfers',
        ],
        'Đơn hàng Bán (Orders)' => ['view_orders', 'create_orders', 'edit_orders', 'process_orders', 'delete_orders'],
    ];

    // Chuyển object permissions thành array chỉ chứa tên để dễ check in_array
    $availablePermissions = $permissions->pluck('name')->toArray();
@endphp

@section('content')
    <x-crud.form :title="$title" :action="$action" :method="$method" cancelRoute="{{ route('roles.index') }}">
        <!-- Khối nhập tên Role -->
        <div class="md:col-span-2 mb-6 bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Thông tin Vai trò
            </h3>

            <div class="w-full md:w-1/2">
                <x-ui.input name="name" label="Tên Vai trò (Role Name)" :value="old('name', $role->name ?? '')" required="true"
                    placeholder="VD: manager, warehouse_staff..." :disabled="$isEdit && $role->name === 'admin'" />
                @if ($isEdit && $role->name === 'admin')
                    <div class="mt-2 flex items-start gap-2 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                        <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p>Vai trò mặc định <strong>"admin"</strong> là cố định của hệ thống. Không thể đổi tên và tự động
                            sở hữu toàn bộ quyền.</p>
                    </div>
                    <input type="hidden" name="name" value="admin">
                @endif
            </div>
        </div>

        <!-- Khối chọn Quyền (Permissions) -->
        <div class="md:col-span-2">
            <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-3">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Phân quyền chức năng
                </h3>
                @if (!($isEdit && $role->name === 'admin'))
                    <button type="button" id="btn-check-all"
                        class="text-sm px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-md transition-colors">
                        Chọn tất cả toàn hệ thống
                    </button>
                @endif
            </div>

            @error('permissions')
                <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm font-medium border border-red-100">
                    Vui lòng chọn ít nhất một quyền hoặc kiểm tra lại: {{ $message }}
                </div>
            @enderror

            <!-- Lưới hiển thị các nhóm quyền -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($permissionGroups as $groupName => $groupPerms)
                    <div
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col group/card hover:border-indigo-300 transition-colors">
                        <!-- Header Card -->
                        <div
                            class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex justify-between items-center group-hover/card:bg-indigo-50/50 transition-colors">
                            <h4 class="font-semibold text-gray-800 text-sm">{{ $groupName }}</h4>
                            @if (!($isEdit && $role->name === 'admin'))
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-white px-2 py-1 rounded shadow-sm border border-gray-200">
                                    <input type="checkbox"
                                        class="group-check-all w-3.5 h-3.5 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        data-group-target="group-{{ $loop->index }}">
                                    Tất cả
                                </label>
                            @endif
                        </div>

                        <!-- Danh sách quyền trong nhóm -->
                        <div class="p-4 flex-1">
                            <div class="space-y-3" id="group-{{ $loop->index }}">
                                @foreach ($groupPerms as $perm)
                                    @if (in_array($perm, $availablePermissions))
                                        <label class="flex items-start gap-3 cursor-pointer group/item">
                                            <div class="shrink-0 mt-0.5">
                                                <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                                    class="permission-checkbox w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 transition-colors cursor-pointer disabled:opacity-50"
                                                    {{ in_array($perm, $oldPermissions) ? 'checked' : '' }}
                                                    {{ $isEdit && $role->name === 'admin' ? 'disabled checked' : '' }}>
                                            </div>
                                            <span
                                                class="text-sm text-gray-600 group-hover/item:text-indigo-700 transition-colors leading-tight">
                                                <!-- Đổi format chữ nhìn cho đẹp (ví dụ: view_users -> View Users) -->
                                                {{ str_replace('_', ' ', ucfirst($perm)) }}
                                            </span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Quyền chưa được nhóm (Phòng hờ khai báo thiếu trong mảng) -->
            @php
                $allGroupedPerms = call_user_func_array('array_merge', array_values($permissionGroups));
                $ungroupedPerms = array_diff($availablePermissions, $allGroupedPerms);
            @endphp

            @if (count($ungroupedPerms) > 0)
                <div class="mt-6 bg-orange-50 border border-orange-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="bg-orange-100 border-b border-orange-200 px-4 py-3">
                        <h4 class="font-semibold text-orange-800 text-sm">Các quyền khác (Chưa phân nhóm)</h4>
                    </div>
                    <div class="p-4 flex flex-wrap gap-4" id="group-ungrouped">
                        @foreach ($ungroupedPerms as $perm)
                            <label
                                class="flex items-center gap-2 cursor-pointer bg-white px-3 py-2 border border-gray-200 rounded-lg hover:border-orange-300">
                                <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                    class="permission-checkbox w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-500"
                                    {{ in_array($perm, $oldPermissions) ? 'checked' : '' }}
                                    {{ $isEdit && $role->name === 'admin' ? 'disabled checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $perm }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </x-crud.form>

    <!-- Script xử lý nút Check All -->
    @if (!($isEdit && $role->name === 'admin'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Xử lý Check all cho từng nhóm
                const groupCheckboxes = document.querySelectorAll('.group-check-all');
                groupCheckboxes.forEach(btn => {
                    btn.addEventListener('change', function() {
                        const targetId = this.getAttribute('data-group-target');
                        const checkboxes = document.querySelectorAll(
                            `#${targetId} .permission-checkbox`);
                        checkboxes.forEach(cb => {
                            cb.checked = this.checked;
                        });
                    });
                });

                // Xử lý Check all toàn hệ thống
                const btnCheckAllSystem = document.getElementById('btn-check-all');
                let isAllChecked = false;

                if (btnCheckAllSystem) {
                    btnCheckAllSystem.addEventListener('click', function() {
                        isAllChecked = !isAllChecked;
                        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
                        const allGroupBtns = document.querySelectorAll('.group-check-all');

                        allCheckboxes.forEach(cb => cb.checked = isAllChecked);
                        allGroupBtns.forEach(btn => btn.checked = isAllChecked);

                        this.innerText = isAllChecked ? 'Bỏ chọn tất cả' : 'Chọn tất cả toàn hệ thống';
                        this.className = isAllChecked ?
                            'text-sm px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium rounded-md transition-colors' :
                            'text-sm px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-md transition-colors';
                    });
                }
            });
        </script>
    @endif
@endsection
