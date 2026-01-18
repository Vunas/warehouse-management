<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::with(['product', 'storageBlock.warehouse']);

        // Filter theo Kho
        if ($request->has('warehouse_id')) {
            $query->whereHas('storageBlock', function($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            });
        }

        // Filter theo Sản phẩm
        if ($request->has('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('sku', 'like', '%'.$request->search.'%');
            });
        }

        $items = $query->paginate(20);
        $warehouses = Warehouse::all();

        return view('admin.inventory.index', compact('items', 'warehouses'));
    }
}