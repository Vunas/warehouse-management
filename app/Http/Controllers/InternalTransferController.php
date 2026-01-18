<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InternalTransfer;
use App\Models\StorageBlock;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InternalTransferController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
        // Check permission 'inventory.transfer'
    }

    public function index()
    {
        $transfers = InternalTransfer::with(['fromBlock', 'toBlock'])->latest()->paginate(10);
        return view('admin.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $blocks = StorageBlock::where('status', '!=', 'locked')->get();
        return view('admin.transfers.create', compact('blocks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_block_id' => 'required|exists:storage_blocks,id',
            'to_block_id' => 'required|exists:storage_blocks,id|different:from_block_id',
            'items' => 'required|array', 
            'reason' => 'nullable|string'
        ]);

        $this->inventoryService->createTransfer($data);

        return redirect()->route('transfers.index')->with('success', 'Lệnh chuyển kho đã được tạo.');
    }

    public function complete(InternalTransfer $internalTransfer)
    {
        $this->inventoryService->executeTransfer($internalTransfer->id);
        return back()->with('success', 'Chuyển kho hoàn tất.');
    }
}