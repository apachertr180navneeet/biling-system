<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePartStock;
use Illuminate\Http\Request;

class SparePartStockController extends Controller
{
    public function index()
    {
        $stocks = SparePartStock::with('sparePart', 'purchaseOrder')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.spare_part_stocks.index', compact('stocks'));
    }

    public function toggleStatus(SparePartStock $sparePartStock)
    {
        $sparePartStock->update(['is_active' => !$sparePartStock->is_active]);
        return response()->json(['success' => true, 'is_active' => $sparePartStock->fresh()->is_active]);
    }

    public function destroy(SparePartStock $sparePartStock)
    {
        $sparePartStock->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
}
