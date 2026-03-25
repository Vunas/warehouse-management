<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $warehouseId = $request->get('warehouse_id', Warehouse::first()?->id);

        $warehouses = Warehouse::all();
        $locationsTree = Location::where('warehouse_id', $warehouseId)
            ->whereNull('parent_id')
            ->with('children_tree')
            ->get();

        $flatLocations = Location::where('warehouse_id', $warehouseId)->get();

        return view('admin.locations.index', compact('warehouses', 'warehouseId', 'locationsTree', 'flatLocations'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $flatLocations = Location::all();
        return view('admin.locations.create', compact('warehouses', 'flatLocations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'parent_id' => 'nullable|exists:locations,id',
            'is_store' => 'nullable|boolean',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index', ['warehouse_id' => $validated['warehouse_id']])
            ->with('success', 'Thêm vị trí thành công!');
    }

    public function edit(Location $location)
    {
        $warehouses = Warehouse::all();
        $flatLocations = Location::where('warehouse_id', $location->warehouse_id)->get();

        return view('admin.locations.edit', compact('location', 'warehouses', 'flatLocations'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'parent_id' => 'nullable|exists:locations,id',
            'is_store' => 'nullable|boolean',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index', ['warehouse_id' => $location->warehouse_id])
            ->with('success', 'Cập nhật vị trí thành công!');
    }

    public function destroy(Location $location)
    {
        $warehouseId = $location->warehouse_id;
        $location->delete();

        return redirect()->route('locations.index', ['warehouse_id' => $warehouseId])
            ->with('success', 'Xóa vị trí thành công!');
    }
}
