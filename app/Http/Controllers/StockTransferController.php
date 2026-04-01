<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockTransfer\StoreStockTransferRequest;
use App\Http\Requests\StockTransfer\AddTransferItemRequest;
use App\Http\Requests\StockTransfer\UpdateBulkTransferRequest;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class StockTransferController extends Controller
{
    protected $transferService;

    public function __construct(StockTransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $transfers = $this->transferService->getPaginatedTransfers($filters, $request->get('per_page', 15));

        return view('admin.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $data = $this->transferService->getCreateData();
        return view('admin.transfers.create', $data);
    }

    public function store(StoreStockTransferRequest $request)
    {
        try {
            $data = $request->validated();
            $data['staff_id'] = Auth::id();

            $transfer = $this->transferService->createTransfer($data);

            return redirect()
                ->route('transfers.show', $transfer->id)
                ->with('success', 'Khởi tạo phiếu chuyển thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $data = $this->transferService->getShowData($id);
        return view('admin.transfers.show', $data);
    }

    public function addItem(AddTransferItemRequest $request, $id)
    {
        try {
            $this->transferService->autoAllocateAndAddItems(
                $id,
                $request->product_id,
                $request->quantity
            );

            return back()->with('success', 'Đã thêm vào phiếu dự tính (chưa trừ kho).');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateBulk(UpdateBulkTransferRequest $request, $id)
    {
        try {
            $this->transferService->updateBulkItems($id, $request->items);
            return back()->with('success', 'Đã lưu tạm thời toàn bộ lộ trình.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeItem($id, $itemId)
    {
        try {
            $this->transferService->removeItem($itemId);
            return back()->with('success', 'Đã xóa dòng khỏi phiếu.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete($id)
    {
        try {
            $this->transferService->completeTransfer($id);
            return back()->with('success', 'Chuyển kho THÀNH CÔNG! Đã trừ kho thực tế.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            $this->transferService->cancelTransfer($id);
            return back()->with('success', 'Đã hủy phiếu nháp!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}   