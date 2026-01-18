<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Danh sách tất cả các quyền trong hệ thống
        $permissions = [
            // --- Quản lý Nhân viên (Employee) ---
            ['code' => 'employee.view', 'description' => 'Xem danh sách nhân viên'],
            ['code' => 'employee.create', 'description' => 'Thêm nhân viên mới'],
            ['code' => 'employee.update', 'description' => 'Cập nhật thông tin nhân viên'],
            ['code' => 'employee.delete', 'description' => 'Xóa/Khóa nhân viên'],

            ['code' => 'role.view', 'description' => 'Xem danh sách quyền'],
            ['code' => 'role.create', 'description' => 'Thêm quyền mới'],
            ['code' => 'role.update', 'description' => 'Cập nhật thông tin quyền'],
            ['code' => 'role.delete', 'description' => 'Xóa/Khóa quyền'],

            // --- Quản lý Kho & Sơ đồ (Warehouse) ---
            ['code' => 'warehouse.view', 'description' => 'Xem danh sách kho và sơ đồ trực quan'],
            ['code' => 'warehouse.create', 'description' => 'Tạo kho mới'],
            ['code' => 'warehouse.update', 'description' => 'Cập nhật cấu trúc kho (Blocks/Slots)'],

            // --- Quản lý Hợp đồng (Contract) ---
            ['code' => 'contract.view', 'description' => 'Xem danh sách hợp đồng'],
            ['code' => 'contract.create', 'description' => 'Tạo hợp đồng thuê kho'],
            ['code' => 'contract.update', 'description' => 'Cập nhật hợp đồng'],
            ['code' => 'contract.approve', 'description' => 'Duyệt và kích hoạt hợp đồng'],
            ['code' => 'contract.delete', 'description' => 'Hủy/Xóa hợp đồng'],

            // --- Nhập kho (Inbound) ---
            ['code' => 'inbound.view', 'description' => 'Xem phiếu nhập kho'],
            ['code' => 'inbound.create', 'description' => 'Tạo yêu cầu nhập kho'],
            ['code' => 'inbound.approve', 'description' => 'Duyệt yêu cầu nhập (Tính toán slot)'],
            ['code' => 'inbound.process', 'description' => 'Thực hiện nhập kho (Confirm đã nhận hàng)'],

            // --- Xuất kho (Outbound) ---
            ['code' => 'outbound.view', 'description' => 'Xem phiếu xuất kho'],
            ['code' => 'outbound.create', 'description' => 'Tạo yêu cầu xuất kho'],
            ['code' => 'outbound.approve', 'description' => 'Duyệt yêu cầu xuất kho'],
            ['code' => 'outbound.process', 'description' => 'Thực hiện xuất kho (Confirm đã lấy hàng)'],

            // --- Tồn kho & Điều chuyển (Inventory) ---
            ['code' => 'inventory.view', 'description' => 'Xem chi tiết tồn kho'],
            ['code' => 'inventory.transfer', 'description' => 'Tạo lệnh chuyển kho nội bộ'],
            ['code' => 'inventory.adjust', 'description' => 'Điều chỉnh tồn kho (Kiểm kê/Bù trừ)'],

            // --- Báo cáo (Report) ---
            ['code' => 'report.view', 'description' => 'Xem báo cáo doanh thu và hiệu suất'],
        ];

        // 2. Insert Permissions vào Database
        foreach ($permissions as $perm) {
            $perm['guard_name'] = 'web';
            $perm['created_at'] = now();
            $perm['updated_at'] = now();

            DB::table('permissions')->updateOrInsert(
                ['code' => $perm['code']],
                $perm
            );
        }

        // 3. Phân quyền tự động cho các Role (Demo logic)
        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles()
    {
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        $managerRole = DB::table('roles')->where('name', 'Manager')->first();
        $staffRole = DB::table('roles')->where('name', 'Staff')->first();

        $allPermissions = DB::table('permissions')->pluck('id');

        // --- A. Role ADMIN: Full quyền ---
        if ($adminRole) {
            foreach ($allPermissions as $permId) {
                DB::table('permission_role')->updateOrInsert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permId
                ]);
            }
        }

        // --- B. Role MANAGER: Full quyền trừ việc xóa User hệ thống ---
        if ($managerRole) {
            $managerPermissions = DB::table('permissions')
                ->where('code', '!=', 'employee.delete') 
                ->pluck('id');

            foreach ($managerPermissions as $permId) {
                DB::table('permission_role')->updateOrInsert([
                    'role_id' => $managerRole->id,
                    'permission_id' => $permId
                ]);
            }
        }

        // --- C. Role STAFF: Chỉ thao tác vận hành (Kho, Nhập, Xuất) ---
        if ($staffRole) {
            $staffCodes = [
                'warehouse.view',      
                'inbound.view',
                'inbound.process', 
                'outbound.view',
                'outbound.process', 
                'inventory.view',
                'inventory.transfer', 
            ];

            $staffPermissions = DB::table('permissions')
                ->whereIn('code', $staffCodes)
                ->pluck('id');

            foreach ($staffPermissions as $permId) {
                DB::table('permission_role')->updateOrInsert([
                    'role_id' => $staffRole->id,
                    'permission_id' => $permId
                ]);
            }
        }
    }
}
