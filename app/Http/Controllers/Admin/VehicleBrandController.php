<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleBrand;
use Illuminate\Http\Request;

class VehicleBrandController extends Controller
{
    public function index()
    {
        $brands = VehicleBrand::orderBy('name')->paginate(20);
        return view('admin.vehicle_brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.vehicle_brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:vehicle_brands']);
        VehicleBrand::create($data);
        return redirect()->route('admin.vehicle-brands.index')->withSuccess('Brand created successfully.');
    }

    public function edit(VehicleBrand $vehicleBrand)
    {
        return view('admin.vehicle_brands.edit', ['brand' => $vehicleBrand]);
    }

    public function update(Request $request, VehicleBrand $vehicleBrand)
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:vehicle_brands,name,' . $vehicleBrand->id]);
        $vehicleBrand->update($data);
        return redirect()->route('admin.vehicle-brands.index')->withSuccess('Brand updated successfully.');
    }

    public function destroy(VehicleBrand $vehicleBrand)
    {
        $vehicleBrand->delete();
        return response()->json(['success' => true, 'message' => 'Brand deleted successfully.']);
    }

    public function toggleStatus(VehicleBrand $vehicleBrand)
    {
        $vehicleBrand->update(['is_active' => !$vehicleBrand->is_active]);
        return response()->json(['success' => true, 'is_active' => $vehicleBrand->fresh()->is_active]);
    }
}
