<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePartStock;
use App\Models\SparePart;
use Illuminate\Http\Request;

class SparePartStockController extends Controller
{
    public function index()
    {
        $stocks = SparePartStock::with('sparePart.category')->orderBy('spare_part_id')->paginate(20);
        return view('admin.spare_part_stocks.index', compact('stocks'));
    }

    public function create()
    {
        $spareParts = SparePart::with('category')->orderBy('name')->get();
        return view('admin.spare_part_stocks.create', compact('spareParts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id|unique:spare_part_stocks,spare_part_id',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);
        SparePartStock::create($data);
        return redirect()->route('admin.spare-part-stocks.index')->withSuccess('Stock created successfully.');
    }

    public function edit(SparePartStock $sparePartStock)
    {
        $spareParts = SparePart::with('category')->orderBy('name')->get();
        return view('admin.spare_part_stocks.edit', compact('sparePartStock', 'spareParts'));
    }

    public function update(Request $request, SparePartStock $sparePartStock)
    {
        $data = $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id|unique:spare_part_stocks,spare_part_id,' . $sparePartStock->id,
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);
        $sparePartStock->update($data);
        return redirect()->route('admin.spare-part-stocks.index')->withSuccess('Stock updated successfully.');
    }

    public function destroy(SparePartStock $sparePartStock)
    {
        $sparePartStock->delete();
        return response()->json(['success' => true, 'message' => 'Stock deleted successfully.']);
    }

    public function toggleStatus(SparePartStock $sparePartStock)
    {
        $sparePartStock->update(['is_active' => !$sparePartStock->is_active]);
        return response()->json(['success' => true, 'is_active' => $sparePartStock->fresh()->is_active]);
    }
}
