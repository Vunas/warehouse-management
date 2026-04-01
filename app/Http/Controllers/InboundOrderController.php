<?php

namespace App\Http\Controllers;

use App\Http\Requests\InboundOrder\AddInboundItemRequest;
use App\Http\Requests\InboundOrder\CompleteInboundOrderRequest;
use App\Services\InboundOrderService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class InboundOrderController extends Controller
{
    protected $inboundOrderService;

    public function __construct(InboundOrderService $inboundOrderService)
    {
        $this->inboundOrderService = $inboundOrderService;
    }

    public function index(Request $request)
    {
        $inbounds = $this->inboundOrderService->getPaginatedInbounds($request->get('per_page', 15));
        return view('admin.inbounds.index', compact('inbounds'));
    }

    public function create()
    {
        $data = $this->inboundOrderService->getCreateData();
        return view('admin.inbounds.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate(['supplier_id' => 'required|exists:suppliers,id']);
        try {
            $data = $request->only('supplier_id');
            $data['staff_id'] = Auth::id();
            $inbound = $this->inboundOrderService->createInboundOrder($data);
            
            return redirect()->route('inbounds.show', $inbound->id)->with('success', 'Khởi tạo phiếu nhập kho thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $data = $this->inboundOrderService->getShowData($id);
        return view('admin.inbounds.show', $data);
    }

    public function addItem(AddInboundItemRequest $request, $id)
    {
        try {
            $this->inboundOrderService->addItem($id, $request->validated());
            return back()->with('success', 'Đã thêm sản phẩm vào phiếu nhập.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateItem(Request $request, $id, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'price'    => 'required|numeric|min:0',
        ]);

        try {
            $this->inboundOrderService->updateItem($id, $itemId, $request->only('quantity', 'price'));
            return back()->with('success', 'Đã cập nhật số lượng và đơn giá.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeItem($id, $itemId)
    {
        try {
            $this->inboundOrderService->removeItem($id, $itemId);
            return back()->with('success', 'Đã xóa sản phẩm khỏi phiếu.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(CompleteInboundOrderRequest $request, $id)
    {
        try {
            $this->inboundOrderService->completeInboundOrder($id, $request->assignments);
            return back()->with('success', 'Hoàn tất nhập kho, tồn kho đã được cộng!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            $this->inboundOrderService->cancelInboundOrder($id);
            return back()->with('success', 'Đã hủy phiếu nhập kho!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}