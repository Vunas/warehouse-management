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
        // Lấy toàn bộ các field cần thiết cho cả lọc cơ bản và nâng cao
        $filters = $request->only([
            'type', 
            'search', 
            'date_from', 
            'date_to', 
            'price_from', 
            'price_to'
        ]);

        $transactions = $this->transactionService->getFilteredTransactions($filters, 20);

        // Kiểm tra xem user có đang dùng bộ lọc nâng cao không để mở sẵn UI
        $hasAdvancedFilters = $request->filled('date_from') || $request->filled('date_to') || $request->filled('price_from') || $request->filled('price_to');

        return view('admin.inventory_transactions.index', compact('transactions', 'hasAdvancedFilters'));
    }
}