<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    public function index()
    {
        $parts = SparePart::orderBy('name')->paginate(20);
        return view('admin.spare_parts.index', compact('parts'));
    }

    public function create()
    {
        return view('admin.spare_parts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'part_no' => 'required|string|max:100|unique:spare_parts',
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10',
        ]);
        SparePart::create($data);
        return redirect()->route('admin.spare-parts.index')->withSuccess('Spare part created successfully.');
    }

    public function edit(SparePart $sparePart)
    {
        return view('admin.spare_parts.edit', compact('sparePart'));
    }

    public function update(Request $request, SparePart $sparePart)
    {
        $data = $request->validate([
            'part_no' => 'required|string|max:100|unique:spare_parts,part_no,' . $sparePart->id,
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10',
        ]);
        $sparePart->update($data);
        return redirect()->route('admin.spare-parts.index')->withSuccess('Spare part updated successfully.');
    }

    public function destroy(SparePart $sparePart)
    {
        $sparePart->delete();
        return response()->json(['success' => true, 'message' => 'Spare part deleted successfully.']);
    }

    public function toggleStatus(SparePart $sparePart)
    {
        $sparePart->update(['is_active' => !$sparePart->is_active]);
        return response()->json(['success' => true, 'is_active' => $sparePart->fresh()->is_active]);
    }
}
