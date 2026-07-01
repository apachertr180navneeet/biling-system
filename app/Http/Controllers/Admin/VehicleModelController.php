<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    public function index()
    {
        $models = VehicleModel::with('brand')->orderBy('name')->paginate(20);
        return view('admin.vehicle_models.index', compact('models'));
    }

    public function create()
    {
        $brands = VehicleBrand::orderBy('name')->get();
        return view('admin.vehicle_models.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:vehicle_brands,id',
            'name' => 'required|string|max:255',
            'body_type' => 'nullable|string|max:100',
        ]);
        VehicleModel::create($data);
        return redirect()->route('admin.vehicle-models.index')->withSuccess('Model created successfully.');
    }

    public function edit(VehicleModel $vehicleModel)
    {
        $brands = VehicleBrand::orderBy('name')->get();
        return view('admin.vehicle_models.edit', compact('vehicleModel', 'brands'));
    }

    public function update(Request $request, VehicleModel $vehicleModel)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:vehicle_brands,id',
            'name' => 'required|string|max:255',
            'body_type' => 'nullable|string|max:100',
        ]);
        $vehicleModel->update($data);
        return redirect()->route('admin.vehicle-models.index')->withSuccess('Model updated successfully.');
    }

    public function destroy(VehicleModel $vehicleModel)
    {
        $vehicleModel->delete();
        return response()->json(['success' => true, 'message' => 'Model deleted successfully.']);
    }

    public function toggleStatus(VehicleModel $vehicleModel)
    {
        $vehicleModel->update(['is_active' => !$vehicleModel->is_active]);
        return response()->json(['success' => true, 'is_active' => $vehicleModel->fresh()->is_active]);
    }
}
