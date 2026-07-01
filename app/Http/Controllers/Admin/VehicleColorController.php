<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleColor;
use App\Models\VehicleVariant;
use Illuminate\Http\Request;

class VehicleColorController extends Controller
{
    public function index()
    {
        $colors = VehicleColor::with('variant.model.brand')->orderBy('color_name')->paginate(20);
        return view('admin.vehicle_colors.index', compact('colors'));
    }

    public function create()
    {
        $variants = VehicleVariant::with('model.brand')->orderBy('name')->get();
        return view('admin.vehicle_colors.create', compact('variants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|exists:vehicle_variants,id',
            'color_name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);
        VehicleColor::create($data);
        return redirect()->route('admin.vehicle-colors.index')->withSuccess('Color created successfully.');
    }

    public function edit(VehicleColor $vehicleColor)
    {
        $variants = VehicleVariant::with('model.brand')->orderBy('name')->get();
        return view('admin.vehicle_colors.edit', compact('vehicleColor', 'variants'));
    }

    public function update(Request $request, VehicleColor $vehicleColor)
    {
        $data = $request->validate([
            'variant_id' => 'required|exists:vehicle_variants,id',
            'color_name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);
        $vehicleColor->update($data);
        return redirect()->route('admin.vehicle-colors.index')->withSuccess('Color updated successfully.');
    }

    public function destroy(VehicleColor $vehicleColor)
    {
        $vehicleColor->delete();
        return response()->json(['success' => true, 'message' => 'Color deleted successfully.']);
    }

    public function toggleStatus(VehicleColor $vehicleColor)
    {
        $vehicleColor->update(['is_active' => !$vehicleColor->is_active]);
        return response()->json(['success' => true, 'is_active' => $vehicleColor->fresh()->is_active]);
    }
}
