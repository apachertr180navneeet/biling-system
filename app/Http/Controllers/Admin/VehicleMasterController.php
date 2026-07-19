<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleMaster;
use Illuminate\Http\Request;

class VehicleMasterController extends Controller
{
    public function index()
    {
        $vehicles = VehicleMaster::orderBy('variant_name')->paginate(20);
        return view('admin.vehicle_masters.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicle_masters.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'variant_name' => 'nullable|string|max:255',
            'color_name' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:255',
            'ex_showroom_price' => 'required|numeric|min:0',
        ]);
        VehicleMaster::create($data);
        return redirect()->route('admin.vehicle-masters.index')->withSuccess('Vehicle master created successfully.');
    }

    public function edit(VehicleMaster $vehicleMaster)
    {
        return view('admin.vehicle_masters.edit', ['vehicle' => $vehicleMaster]);
    }

    public function update(Request $request, VehicleMaster $vehicleMaster)
    {
        $data = $request->validate([
            'variant_name' => 'nullable|string|max:255',
            'color_name' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:255',
            'ex_showroom_price' => 'required|numeric|min:0',
        ]);
        $vehicleMaster->update($data);
        return redirect()->route('admin.vehicle-masters.index')->withSuccess('Vehicle master updated successfully.');
    }

    public function destroy(VehicleMaster $vehicleMaster)
    {
        $vehicleMaster->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(VehicleMaster $vehicleMaster)
    {
        $vehicleMaster->update(['is_active' => !$vehicleMaster->is_active]);
        return response()->json(['success' => true, 'is_active' => $vehicleMaster->is_active]);
    }
}
