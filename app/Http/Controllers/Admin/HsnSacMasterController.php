<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HsnSacMaster;
use Illuminate\Http\Request;

class HsnSacMasterController extends Controller
{
    public function index()
    {
        $hsnCodes = HsnSacMaster::orderBy('code')->paginate(20);
        return view('admin.hsn_sac_master.index', compact('hsnCodes'));
    }

    public function create()
    {
        return view('admin.hsn_sac_master.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:8|unique:hsn_sac_master',
            'description' => 'nullable|string|max:500',
            'gst_rate' => 'required|numeric|min:0|max:100',
            'cess_rate' => 'required|numeric|min:0|max:100',
        ]);
        HsnSacMaster::create($data);
        return redirect()->route('admin.hsn-sac-master.index')->withSuccess('HSN/SAC code created successfully.');
    }

    public function edit(HsnSacMaster $hsnSacMaster)
    {
        return view('admin.hsn_sac_master.edit', ['hsn' => $hsnSacMaster]);
    }

    public function update(Request $request, HsnSacMaster $hsnSacMaster)
    {
        $data = $request->validate([
            'code' => 'required|string|max:8|unique:hsn_sac_master,code,' . $hsnSacMaster->id,
            'description' => 'nullable|string|max:500',
            'gst_rate' => 'required|numeric|min:0|max:100',
            'cess_rate' => 'required|numeric|min:0|max:100',
        ]);
        $hsnSacMaster->update($data);
        return redirect()->route('admin.hsn-sac-master.index')->withSuccess('HSN/SAC code updated successfully.');
    }

    public function destroy(HsnSacMaster $hsnSacMaster)
    {
        $hsnSacMaster->delete();
        return response()->json(['success' => true, 'message' => 'HSN/SAC code deleted successfully.']);
    }

    public function toggleStatus(HsnSacMaster $hsnSacMaster)
    {
        $hsnSacMaster->update(['is_active' => !$hsnSacMaster->is_active]);
        return response()->json(['success' => true, 'is_active' => $hsnSacMaster->fresh()->is_active]);
    }
}
