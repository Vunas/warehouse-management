<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Models\Contract;
use App\Services\ContractService;
use App\Services\CustomerService;
use App\Services\WarehouseService;

class ContractController extends Controller
{
    protected $contractService;
    protected $customerService;
    protected $warehouseService;

    public function __construct(
        ContractService $contractService,
        CustomerService $customerService,
        WarehouseService $warehouseService
    ) {
        $this->contractService = $contractService;
        $this->customerService = $customerService;
        $this->warehouseService = $warehouseService;
        $this->authorizeResource(Contract::class, 'contract');
    }

    public function index()
    {
        $contracts = $this->contractService->getContractsPaginated();
        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $customers = $this->customerService->getCustomerSelection();
        $warehouses = $this->warehouseService->getRentableWarehousesWithAvailableBlocks();

        return view('admin.contracts.create', compact('customers', 'warehouses'));
    }

    public function store(StoreContractRequest $request)
    {
        $this->contractService->createContract($request->validated());

        return redirect()
            ->route('contracts.index')
            ->with('success', 'Tạo hợp đồng thành công');
    }

    public function show(Contract $contract)
    {

        $contract->load([
            'customer',
            'contractBlocks.storageBlock.warehouse',
            'inboundTickets',
            'outboundTickets',
        ]);

        return view('admin.contracts.show', compact('contract'));
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $this->contractService->updateContract($contract->id, $request->validated());

        return redirect()
            ->route('contracts.index')
            ->with('success', 'Cập nhật hợp đồng thành công');
    }

    public function destroy(Contract $contract)
    {
        $this->contractService->deleteContract($contract->id);

        return redirect()
            ->route('contracts.index')
            ->with('success', 'Đã xóa hợp đồng thành công.');
    }
}
