<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OutboundTicket;
use App\Models\Contract;
use App\Services\OutboundService;
use Illuminate\Http\Request;

class OutboundTicketController extends Controller
{
    protected $outboundService;

    public function __construct(OutboundService $outboundService)
    {
        $this->outboundService = $outboundService;
        $this->authorizeResource(OutboundTicket::class, 'outbound_ticket');
    }

    public function index()
    {
        $tickets = OutboundTicket::with('contract.customer')->latest()->paginate(15);
        return view('admin.outbound.index', compact('tickets'));
    }

    public function create()
    {
        $contracts = Contract::where('status', 'active')->get();
        return view('admin.outbound.create', compact('contracts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'requested_date' => 'required|date',
            'products' => 'required|array', 
        ]);

        $this->outboundService->createTicket($data);
        return redirect()->route('outbound_tickets.index')->with('success', 'Tạo phiếu xuất thành công');
    }

    public function show(OutboundTicket $outboundTicket)
    {
        $outboundTicket->load('details.product');
        return view('admin.outbound.show', compact('outboundTicket'));
    }

    public function process(OutboundTicket $outboundTicket)
    {
        // Logic trừ tồn kho (FIFO)
        $this->outboundService->processOutbound($outboundTicket->id);
        return back()->with('success', 'Xuất kho hoàn tất.');
    }
}