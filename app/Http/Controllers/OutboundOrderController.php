<?php

namespace App\Http\Controllers;

use App\Http\Requests\Outbound\StoreOutboundOrderRequest;
use App\Services\OutboundOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class OutboundOrderController extends Controller
{
    protected $outboundService;

    public function __construct(OutboundOrderService $outboundService)
    {
        $this->outboundService = $outboundService;
    }

    // API lấy tồn kho theo Warehouse (Phục vụ FEFO)
    public function getInventoryApi($warehouseId)
    {
        try {
            $inventory = $this->outboundService->getInventoryForDropdown($warehouseId);
            return response()->json($inventory);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // TÍNH NĂNG MỚI: API lấy chi tiết Order để tự động điền vào Phiếu xuất
    public function getOrderItemsApi($orderId)
    {
        try {
            $items = $this->outboundService->getOrderItemsApi($orderId);
            return response()->json($items);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $outbounds = $this->outboundService->getPaginatedOutbounds($request->get('per_page', 15));
        return view('admin.outbounds.index', compact('outbounds'));
    }

    public function create()
    {
        $data = $this->outboundService->getFormData();
        return view('admin.outbounds.form', $data);
    }

    public function store(StoreOutboundOrderRequest $request)
    {
        try {
            $data = $request->validated();
            $data['staff_id'] = Auth::id();
            $items = $request->input('items', []);

            $outbound = $this->outboundService->createOutboundOrder($data, $items);

            return redirect()->route('outbounds.show', $outbound->id)
                ->with('success', 'Tạo phiếu xuất và phân bổ hàng hóa thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $outbound = $this->outboundService->getShowData($id);
        
        if ($outbound->status !== 'pending') {
            return back()->with('error', 'Chỉ được sửa phiếu đang chờ xuất!');
        }

        $data = array_merge(['outbound' => $outbound], $this->outboundService->getFormData());
        return view('admin.outbounds.form', $data);
    }

    public function update(StoreOutboundOrderRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $items = $request->input('items', []);

            $this->outboundService->updateOutboundOrder($id, $data, $items);

            return redirect()->route('outbounds.show', $id)->with('success', 'Cập nhật Phiếu xuất thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $outbound = $this->outboundService->getShowData($id);
        return view('admin.outbounds.show', compact('outbound'));
    }

    public function complete($id)
    {
        try {
            $this->outboundService->completeOutboundOrder($id);
            return back()->with('success', 'Đã chốt xuất kho! Hàng thực tế đã được trừ.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->outboundService->cancelOutboundOrder($id);
            return back()->with('success', 'Đã hủy phiếu và nhả lại tồn kho dự kiến.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}