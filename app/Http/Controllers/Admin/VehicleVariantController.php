<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleModel;
use App\Models\VehicleVariant;
use Illuminate\Http\Request;

class VehicleVariantController extends Controller
{
    public function index()
    {
        $variants = VehicleVariant::with('model.brand')->orderBy('name')->paginate(20);
        return view('admin.vehicle_variants.index', compact('variants'));
    }

    public function create()
    {
        $models = VehicleModel::with('brand')->orderBy('name')->get();
        return view('admin.vehicle_variants.create', compact('models'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'model_id' => 'required|exists:vehicle_models,id',
            'name' => 'required|string|max:255',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'ex_showroom_price' => 'required|numeric|min:0',
            'hsn_code' => 'nullable|string|max:8',
        ]);
        VehicleVariant::create($data);
        return redirect()->route('admin.vehicle-variants.index')->withSuccess('Variant created successfully.');
    }

    public function edit(VehicleVariant $vehicleVariant)
    {
        $models = VehicleModel::with('brand')->orderBy('name')->get();
        return view('admin.vehicle_variants.edit', compact('vehicleVariant', 'models'));
    }

    public function update(Request $request, VehicleVariant $vehicleVariant)
    {
        $data = $request->validate([
            'model_id' => 'required|exists:vehicle_models,id',
            'name' => 'required|string|max:255',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'ex_showroom_price' => 'required|numeric|min:0',
            'hsn_code' => 'nullable|string|max:8',
        ]);
        $vehicleVariant->update($data);
        return redirect()->route('admin.vehicle-variants.index')->withSuccess('Variant updated successfully.');
    }

    public function destroy(VehicleVariant $vehicleVariant)
    {
        $vehicleVariant->delete();
        return response()->json(['success' => true, 'message' => 'Variant deleted successfully.']);
    }

    public function toggleStatus(VehicleVariant $vehicleVariant)
    {
        $vehicleVariant->update(['is_active' => !$vehicleVariant->is_active]);
        return response()->json(['success' => true, 'is_active' => $vehicleVariant->fresh()->is_active]);
    }
}
