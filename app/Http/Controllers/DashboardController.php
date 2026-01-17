<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InboundTicket;
use App\Models\StorageBlock;
use App\Models\InventoryItem;
use App\Models\Contract;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê Phiếu nhập đang chờ (Pending)
        $pendingInboundCount = InboundTicket::where('status', 'pending')->count();

        // 2. Thống kê Sức chứa toàn hệ thống
        $totalSlots = StorageBlock::sum('total_slots');
        
        // 3. Tính số slot đã sử dụng (dựa trên Inventory Items)
        $usedSlots = InventoryItem::sum('slot_used');
        
        // 4. Tính slot trống
        $freeSlots = $totalSlots - $usedSlots;

        // 5. Doanh thu dự kiến (Tổng giá trị thuê của các Hợp đồng Active)
        // Lưu ý: Đây là logic đơn giản, thực tế cần tính theo tháng
        $activeContractsCount = Contract::where('status', 'active')->count();

        // 6. Lấy 5 phiếu nhập mới nhất để hiển thị ra bảng
        $latestInbounds = InboundTicket::with('contract.customer.user')
            ->latest()
            ->take(5)
            ->get();

        // Gom dữ liệu vào mảng stats
        $stats = [
            'pending_inbound' => $pendingInboundCount,
            'total_slots' => $totalSlots,
            'used_slots' => $usedSlots,
            'free_slots' => $freeSlots,
            'active_contracts' => $activeContractsCount,
        ];

        return view('admin.dashboard', compact('stats', 'latestInbounds'));
    }
}