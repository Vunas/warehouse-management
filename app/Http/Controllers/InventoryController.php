<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventories = Inventory::with(['product', 'location.warehouse'])
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 15));

        return view('admin.inventory.index', compact('inventories'));
    }

    public function create()
    {
        $locations = Location::all();
        $products = \App\Models\Product::all();
        return view('admin.inventory.form', compact('locations', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'quantity'    => 'required|integer|min:0',
        ]);

        try {
            Inventory::create($request->all());
            return redirect()->route('inventory.index')->with('success', 'Thêm tồn kho thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $inventory = Inventory::with(['product', 'location.warehouse'])->findOrFail($id);
        return view('admin.inventory.show', compact('inventory'));
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        $locations = Location::all();
        $products = Product::all();
        return view('admin.inventory.form', compact('inventory', 'locations', 'products'));
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->update($request->all());
        return redirect()->route('inventory.index')->with('success', 'Cập nhật tồn kho thành công!');
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();
        return back()->with('success', 'Xóa tồn kho thành công!');
    }
}
