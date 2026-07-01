<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleStock;
use App\Models\VehicleColor;
use Illuminate\Http\Request;

class VehicleStockController extends Controller
{
    public function index()
    {
        $stocks = VehicleStock::with('color.variant.model.brand')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.vehicle_stocks.index', compact('stocks'));
    }

    public function create()
    {
        $colors = VehicleColor::with('variant.model.brand')->orderBy('color_name')->get();
        return view('admin.vehicle_stocks.create', compact('colors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'chassis_number' => 'required|string|max:255|unique:vehicle_stocks',
            'engine_number' => 'nullable|string|max:255',
            'color_id' => 'required|exists:vehicle_colors,id',
            'mfg_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'required|numeric|min:0',
            'status' => 'required|in:available,sold,transferred',
            'notes' => 'nullable|string',
        ]);
        VehicleStock::create($data);
        return redirect()->route('admin.vehicle-stocks.index')->withSuccess('Stock created successfully.');
    }

    public function edit(VehicleStock $vehicleStock)
    {
        $colors = VehicleColor::with('variant.model.brand')->orderBy('color_name')->get();
        return view('admin.vehicle_stocks.edit', compact('vehicleStock', 'colors'));
    }

    public function update(Request $request, VehicleStock $vehicleStock)
    {
        $data = $request->validate([
            'chassis_number' => 'required|string|max:255|unique:vehicle_stocks,chassis_number,' . $vehicleStock->id,
            'engine_number' => 'nullable|string|max:255',
            'color_id' => 'required|exists:vehicle_colors,id',
            'mfg_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'required|numeric|min:0',
            'status' => 'required|in:available,sold,transferred',
            'notes' => 'nullable|string',
        ]);
        $vehicleStock->update($data);
        return redirect()->route('admin.vehicle-stocks.index')->withSuccess('Stock updated successfully.');
    }

    public function destroy(VehicleStock $vehicleStock)
    {
        $vehicleStock->delete();
        return response()->json(['success' => true, 'message' => 'Stock deleted successfully.']);
    }

    public function toggleStatus(VehicleStock $vehicleStock)
    {
        $vehicleStock->update(['is_active' => !$vehicleStock->is_active]);
        return response()->json(['success' => true, 'is_active' => $vehicleStock->fresh()->is_active]);
    }
}
