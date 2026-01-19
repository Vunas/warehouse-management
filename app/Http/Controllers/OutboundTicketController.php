<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outbound\StoreOutboundTicketRequest;
use App\Models\OutboundTicket;
use App\Services\OutboundService;
use App\Services\ContractService;

class OutboundTicketController extends Controller
{
    protected $outboundService;
    protected $contractService;

    public function __construct(
        OutboundService $outboundService,
        ContractService $contractService
    ) {
        $this->outboundService = $outboundService;
        $this->contractService = $contractService;
        $this->authorizeResource(OutboundTicket::class, 'outbound_ticket');
    }

    public function index()
    {
        $tickets = $this->outboundService->getOutboundHistory();
        return view('admin.outbound.index', compact('tickets'));
    }

    public function create()
    {
        $contracts = $this->contractService->getActiveContracts();
        return view('admin.outbound.create', compact('contracts'));
    }

    public function store(StoreOutboundTicketRequest $request)
    {
        $this->outboundService->createTicket($request->validated());

        return redirect()->route('outbound_tickets.index')
            ->with('success', 'Tạo phiếu xuất thành công');
    }

    public function show(OutboundTicket $outboundTicket)
    {
        $outboundTicket->load('details.product');
        return view('admin.outbound.show', compact('outboundTicket'));
    }

    public function process(OutboundTicket $outboundTicket)
    {
        $this->outboundService->processOutbound($outboundTicket->id);
        return back()->with('success', 'Xuất kho hoàn tất.');
    }
}