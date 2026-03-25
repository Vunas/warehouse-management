<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\Location;
use App\Models\Inventory;
use App\Models\TransferItem;
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
        $query = StockTransfer::with(['staff', 'fromLocation', 'toLocation'])->orderBy('id', 'desc');
        
        // Lọc theo Mã phiếu
        if ($request->filled('search')) {
            $searchId = str_replace(['TRF-', 'trf-'], '', $request->search);
            $query->where('id', (int)$searchId);
        }

        // Lọc theo Trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transfers = $query->paginate($request->get('per_page', 15));
            
        return view('admin.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $locations = Location::where('is_store', true)->get();
        return view('admin.transfers.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id'   => 'required|exists:locations,id|different:from_location_id',
        ]);

        try {
            $data = $request->only(['from_location_id', 'to_location_id']);
            $data['staff_id'] = Auth::id();

            $transfer = $this->transferService->createTransfer($data);
            return redirect()->route('transfers.show', $transfer->id)->with('success', 'Khởi tạo phiếu luân chuyển thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transfer = StockTransfer::with(['items.product', 'staff'])->findOrFail($id);
        
        $inventories = Inventory::with('product')
            ->where('location_id', $transfer->from_location_id)
            ->where('quantity', '>', 0)
            ->get();

        return view('admin.transfers.show', compact('transfer', 'inventories'));
    }

    public function addItem(Request $request, $id)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        try {
            $transfer = StockTransfer::findOrFail($id);
            if ($transfer->status !== 'pending') throw new Exception("Không thể thêm sản phẩm vào phiếu đã xử lý.");

            $inventory = Inventory::findOrFail($request->inventory_id);
            if ($inventory->quantity < $request->quantity) {
                 throw new Exception("Số lượng luân chuyển không được vượt quá số lượng đang tồn.");
            }

            $existingItem = TransferItem::where('transfer_id', $id)->where('inventory_id', $inventory->id)->first();
            if ($existingItem) {
                 $newQty = $existingItem->quantity + $request->quantity;
                 if ($newQty > $inventory->quantity) throw new Exception("Tổng số lượng vượt quá tồn kho hiện có.");
                 $existingItem->update(['quantity' => $newQty]);
            } else {
                 TransferItem::create([
                     'transfer_id' => $id, 'inventory_id' => $inventory->id,
                     'product_id'  => $inventory->product_id, 'quantity' => $request->quantity
                 ]);
            }
            return back()->with('success', 'Đã thêm sản phẩm vào phiếu luân chuyển.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeItem($id, $itemId)
    {
        try {
            $item = TransferItem::findOrFail($itemId);
            if ($item->transfer->status !== 'pending') throw new Exception("Phiếu đã khóa, không thể xóa.");
            $item->delete();
            return back()->with('success', 'Đã xóa sản phẩm khỏi phiếu.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete($id)
    {
        try {
            $this->transferService->completeTransfer($id);
            return back()->with('success', 'Luân chuyển hàng hóa thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function cancel($id)
    {
        try {
            $this->transferService->cancelTransfer($id);
            return back()->with('success', 'Đã hủy phiếu nhập kho!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
