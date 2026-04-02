<?php

namespace App\Http\Controllers;

use App\Services\InventoryTransactionService;
use Illuminate\Http\Request;

class InventoryTransactionController extends Controller
{
    protected $transactionService;

    public function __construct(InventoryTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'search']);
        $transactions = $this->transactionService->getFilteredTransactions($filters, 20);

        return view('admin.inventory_transactions.index', compact('transactions'));
    }
}
