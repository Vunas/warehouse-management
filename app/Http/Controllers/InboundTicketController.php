<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inbound\StoreInboundTicketRequest;
use App\Models\InboundTicket;
use App\Services\InboundService;
use App\Services\ProductService;
use App\Services\ContractService; 

class InboundTicketController extends Controller
{
    protected $inboundService;
    protected $productService;
    protected $contractService;

    public function __construct(
        InboundService $inboundService,
        ProductService $productService,
        ContractService $contractService
    ) {
        $this->inboundService = $inboundService;
        $this->productService = $productService;
        $this->contractService = $contractService;
        $this->authorizeResource(InboundTicket::class, 'inbound_ticket');
    }

    public function index()
    {
        $tickets = $this->inboundService->getInboundHistory();
        return view('admin.inbound.index', compact('tickets'));
    }

    public function create()
    {
        // Lấy hợp đồng Active từ Service
        $contracts = $this->contractService->getActiveContracts();
        $products  = $this->productService->getSelectable(); 

        return view('admin.inbound.create', compact('contracts', 'products'));
    }

    public function store(StoreInboundTicketRequest $request)
    {
        $this->inboundService->createTicket($request->validated());

        return redirect()->route('inbound_tickets.index')
            ->with('success', 'Tạo yêu cầu nhập kho thành công');
    }

    public function show(InboundTicket $inboundTicket)
    {
        $inboundTicket = $this->inboundService->getTicketById($inboundTicket->id);
        // $inboundTicket->load(['details.product', 'details.calculatedSlot']);
        
        return view('admin.inbound.show', compact('inboundTicket'));
    }

    public function approve(InboundTicket $inboundTicket)
    {
        $this->inboundService->approveAndCalculateSlots($inboundTicket->id);
        return back()->with('success', 'Đã duyệt phiếu và tính toán vị trí slot.');
    }

    public function process(InboundTicket $inboundTicket)
    {
        $this->inboundService->processReception($inboundTicket->id);
        return back()->with('success', 'Đã xác nhận nhập hàng vào kho.');
    }
}