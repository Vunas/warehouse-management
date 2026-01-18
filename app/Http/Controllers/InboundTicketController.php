<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inbound\StoreInboundTicketRequest;
use App\Models\InboundTicket;
use App\Models\Contract;
use App\Models\Product;
use App\Services\InboundService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class InboundTicketController extends Controller
{
    protected $inboundService;
    protected $productService;


    public function __construct(
        InboundService $inboundService,
        ProductService $productService
    ) {
        $this->inboundService = $inboundService;
        $this->productService = $productService;

        $this->authorizeResource(InboundTicket::class, 'inbound_ticket');
    }


    public function index()
    {
        $tickets = InboundTicket::with('contract.customer')->latest()->paginate(15);
        return view('admin.inbound.index', compact('tickets'));
    }

    public function create()
    {
        $contracts = Contract::where('status', 'active')->get();
        $products  = $this->productService->getAllProducts();

        return view('admin.inbound.create', compact('contracts', 'products'));
    }

    public function store(StoreInboundTicketRequest $request)
    {
        $this->inboundService->createTicket($request->validated());


        return redirect()->route('inbound_tickets.index')->with('success', 'Tạo yêu cầu nhập kho thành công');
    }

    public function show(InboundTicket $inboundTicket)
    {
        $inboundTicket->load(['details.product', 'details.calculatedSlot']);
        return view('admin.inbound.show', compact('inboundTicket'));
    }

    // --- Custom Actions ---

    // 1. Duyệt phiếu & Tính toán slot (Approve)
    public function approve(InboundTicket $inboundTicket)
    {
        $this->inboundService->approveAndCalculateSlots($inboundTicket->id);
        return back()->with('success', 'Đã duyệt phiếu và tính toán vị trí slot.');
    }

    // 2. Xác nhận đã nhận hàng (Process/Receive)
    public function process(InboundTicket $inboundTicket)
    {
        $this->inboundService->processReception($inboundTicket->id);
        return back()->with('success', 'Đã xác nhận nhập hàng vào kho.');
    }
}
