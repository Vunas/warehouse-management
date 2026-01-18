<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\StorageBlock;
use App\Services\ContractService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
        $this->authorizeResource(Contract::class, 'contract');
    }

    public function index()
    {
        $contracts = Contract::with('customer')->latest()->paginate(10);
        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $customers = Customer::all();
        // Chỉ lấy các Block còn trống hoặc phù hợp để thuê
        $availableBlocks = StorageBlock::where('status', 'available')->with('warehouse')->get();
        return view('admin.contracts.create', compact('customers', 'availableBlocks'));
    }

    public function store(Request $request)
    {
        // Validation nên đưa vào ContractRequest
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'contract_code' => 'required|unique:contracts,contract_code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'blocks' => 'required|array', 
            'blocks.*.id' => 'exists:storage_blocks,id',
            'blocks.*.price' => 'required|numeric'
        ]);

        $this->contractService->createContract($data);

        return redirect()->route('contracts.index')->with('success', 'Tạo hợp đồng thành công');
    }

    public function show(Contract $contract)
    {
        $contract->load(['contractBlocks.storageBlock.warehouse', 'inboundTickets', 'outboundTickets']);
        return view('admin.contracts.show', compact('contract'));
    }

    // Edit, Update, Destroy 
}