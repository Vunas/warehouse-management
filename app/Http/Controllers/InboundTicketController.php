<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inbound\StoreInboundTicketRequest;
use App\Http\Requests\Inbound\UpdateInboundTicketRequest; // Import Request Mới
use App\Models\InboundTicket;
use App\Services\InboundService;
use App\Services\ProductService;
use App\Services\ContractService; 
use Exception;

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
        // RBAC Check
        $this->authorizeResource(InboundTicket::class, 'inbound_ticket');
    }

    public function index()
    {
        $tickets = $this->inboundService->getInboundHistory();
        return view('admin.inbound.index', compact('tickets'));
    }

    public function create()
    {
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
        return view('admin.inbound.show', compact('inboundTicket'));
    }

    // --- EDIT ---
    public function edit(InboundTicket $inboundTicket)
    {
        if ($inboundTicket->status !== 'pending') {
            return redirect()->route('inbound_tickets.show', $inboundTicket->id)
                ->with('error', 'Chỉ được sửa phiếu khi đang chờ duyệt.');
        }

        $contracts = $this->contractService->getActiveContracts();
        $products  = $this->productService->getSelectable();
        $inboundTicket->load('details');

        return view('admin.inbound.edit', compact('inboundTicket', 'contracts', 'products'));
    }

    // --- UPDATE ---
    public function update(UpdateInboundTicketRequest $request, InboundTicket $inboundTicket)
    {
        try {
            $this->inboundService->updateTicket($inboundTicket->id, $request->validated());
            return redirect()->route('inbound_tickets.index')
                ->with('success', 'Cập nhật phiếu thành công.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- DESTROY (Soft Delete) ---
    public function destroy(InboundTicket $inboundTicket)
    {
        try {
            $this->inboundService->deleteTicket($inboundTicket->id);
            return redirect()->route('inbound_tickets.index')
                ->with('success', 'Đã xóa phiếu nhập kho.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- APPROVE ---
    public function approve(InboundTicket $inboundTicket)
    {
        try {
            $this->inboundService->approveAndCalculateSlots($inboundTicket->id);
            return back()->with('success', 'Đã duyệt và tính toán slot.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- REJECT (New based on ERD) ---
    public function reject(InboundTicket $inboundTicket)
    {
        try {
            $this->inboundService->rejectTicket($inboundTicket->id);
            return back()->with('success', 'Đã từ chối phiếu nhập kho.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function process(InboundTicket $inboundTicket)
    {
        $this->inboundService->processReception($inboundTicket->id);
        return back()->with('success', 'Đã xác nhận nhập hàng vào kho.');
    }
}