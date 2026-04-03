<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa cache của Spatie để tránh lỗi khi tạo mới
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Tạo danh sách các Permissions
        $permissions = [
            'view_dashboard',    // Xem bảng điều khiển
            'view_reports',      // Xem báo cáo tổng quan trên Dashboard
            // ---------------- Hệ thống & Phân quyền ----------------
            'manage_roles',      // Quản lý vai trò (CRUD Roles)

            // ---------------- Quản lý Người dùng ----------------
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // ---------------- Quản lý Sản phẩm (Products) ----------------
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'manage_brands',    

            // ---------------- Quản lý ProductBatch  ----------------
            'view_product_batches',
            'create_product_batches',
            'edit_product_batches',
            'delete_product_batches',

            // ---------------- Quản lý Nhà cung cấp (Suppliers) ----------------
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',

            // ---------------- Quản lý Kho bãi & Tồn kho (Warehouses & Inventory) ----------------
            'view_warehouses',
            'create_warehouses',
            'edit_warehouses',
            'delete_warehouses',
            'view_inventory',    // Xem tồn kho
            'manage_inventory',  // Điều chỉnh tồn kho thủ công (nếu cần)

            // ---------------- Quản lý Nhập kho (Inbound) ----------------
            'view_inbounds',
            'create_inbounds',
            'edit_inbounds',
            'approve_inbounds',  // Quyền duyệt/hủy phiếu nhập
            'delete_inbounds',

            // ---------------- Quản lý Xuất kho (Outbound) ----------------
            'view_outbounds',
            'create_outbounds',
            'edit_outbounds',
            'approve_outbounds', // Quyền duyệt/hủy phiếu xuất
            'delete_outbounds',

            // ---------------- Quản lý Điều chuyển kho (Stock Transfer) ----------------
            'view_transfers',
            'create_transfers',
            'edit_transfers',
            'approve_transfers', // Quyền duyệt phiếu điều chuyển
            'delete_transfers',

            // ---------------- Lịch sử giao dịch kho (Inventory Transactions) ----------------
            'view_inventory_transactions',
            'create_inventory_transactions',
            'edit_inventory_transactions',
            'delete_inventory_transactions',

            // ---------------- Kiểm kê kho (Stock Takes) ----------------
            'view_stock_takes',
            'create_stock_takes',
            'edit_stock_takes',
            'delete_stock_takes',
            // ---------------- Quản lý Đơn hàng bán (Sales Orders) ----------------
            'view_orders',
            'create_orders',
            'edit_orders',
            'process_orders',    // Xử lý đơn hàng (chuyển trạng thái shipping, completed...)
            'delete_orders',

            // ---------------- Cảnh báo Tồn Kho ----------------
            'view_alerts',      // Xem danh sách và bảng tin cảnh báo
            'create_alerts',    // Tạo cấu hình cảnh báo mới
            'edit_alerts',      // Sửa cấu hình cảnh báo
            'delete_alerts',    // Xóa cấu hình cảnh báo

            
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Tạo Roles (Vai trò) và gán quyền
        // Role Admin: Có tất cả các quyền
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Role Staff (Nhân viên)
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view_dashboard',
            'view_product_batches', 'create_product_batches', 'edit_product_batches', 'delete_product_batches',
            'view_inventory',
            'view_inbounds', 'create_inbounds', 'edit_inbounds', 'approve_inbounds', 'delete_inbounds',
            'view_outbounds', 'create_outbounds', 'edit_outbounds', 'approve_outbounds', 'delete_outbounds',
            'view_transfers', 'create_transfers', 'edit_transfers', 'approve_transfers', 'delete_transfers',
            'view_inventory_transactions',
            'view_stock_takes', 'create_stock_takes',  // Nhân viên có thể tạo phiếu kiểm kê nhưng không được xóa/sửa
        ]);

        // Role customer: cho khách hàng đăng nhập vào hệ thống
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // 3. Tạo một tài khoản Admin mặc định để test
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Kiểm tra xem email này có chưa
            [
                'username'  => 'admin',
                'full_name' => 'Quản trị viên Hệ thống',
                'password'  => Hash::make('123456'), // Mật khẩu mặc định là 123456
                'phone'     => '0123456789',
                'is_active' => true,
            ]
        );

        // Gán vai trò admin cho user này
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole($adminRole);
        }

        // Tạo một tài khoản dev để test
        $exUser = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'username'  => 'customer',
                'full_name' => 'Example customer',
                'phone'     => '0123456788',
                'password'  => Hash::make('123456'),
                'is_active' => true,
            ]
        );
        if (!$exUser->hasRole('customer')) {
            $exUser->assignRole($customerRole);
        }

        $staffUser = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'username'  => 'staff',
                'full_name' => 'Example staff',
                'phone'     => '0123456789',
                'password'  => Hash::make('123456'),
                'is_active' => true,
            ]
        );
        if (!$staffUser->hasRole('staff')) {
            $staffUser->assignRole($staffRole);
        }
    }
}
