<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    public function index()
    {
        $parts = SparePart::with('category')->orderBy('name')->paginate(20);
        return view('admin.spare_parts.index', compact('parts'));
    }

    public function create()
    {
        $categories = SparePartCategory::orderBy('name')->get();
        return view('admin.spare_parts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'part_no' => 'required|string|max:100|unique:spare_parts',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:spare_part_categories,id',
            'hsn_code' => 'nullable|string|max:8',
            'is_gst_applicable' => 'boolean',
            'gst_rate' => 'required|numeric|min:0|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10',
        ]);
        $data['is_gst_applicable'] = $request->boolean('is_gst_applicable');
        SparePart::create($data);
        return redirect()->route('admin.spare-parts.index')->withSuccess('Spare part created successfully.');
    }

    public function edit(SparePart $sparePart)
    {
        $categories = SparePartCategory::orderBy('name')->get();
        return view('admin.spare_parts.edit', compact('sparePart', 'categories'));
    }

    public function update(Request $request, SparePart $sparePart)
    {
        $data = $request->validate([
            'part_no' => 'required|string|max:100|unique:spare_parts,part_no,' . $sparePart->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:spare_part_categories,id',
            'hsn_code' => 'nullable|string|max:8',
            'is_gst_applicable' => 'boolean',
            'gst_rate' => 'required|numeric|min:0|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10',
        ]);
        $data['is_gst_applicable'] = $request->boolean('is_gst_applicable');
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
