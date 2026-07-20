<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SparePartStock;
use App\Models\SparePartStockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SparePartStockController extends Controller
{
    public function index()
    {
        $stocks = SparePartStock::with('sparePart', 'purchaseOrder')->orderBy('created_at', 'desc')->paginate(20);
        $spareParts = \App\Models\SparePart::where('is_active', true)->orderBy('name')->get();
        return view('admin.spare_part_stocks.index', compact('stocks', 'spareParts'));
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

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'adjustment_type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $stock = SparePartStock::firstOrCreate(
                    ['spare_part_id' => $data['spare_part_id']],
                    ['quantity' => 0, 'min_quantity' => 0, 'purchase_price' => 0]
                );

                if ($data['adjustment_type'] === 'out') {
                    if ($stock->quantity < $data['quantity']) {
                        throw new \Exception("Insufficient stock. Current stock is only {$stock->quantity}.");
                    }
                    $stock->decrement('quantity', $data['quantity']);
                } else {
                    $stock->increment('quantity', $data['quantity']);
                }

                SparePartStockTransaction::create([
                    'spare_part_id' => $data['spare_part_id'],
                    'transaction_type' => $data['adjustment_type'],
                    'quantity' => $data['quantity'],
                    'reference_no' => 'ADJ-' . date('YmdHis'),
                    'notes' => $data['notes'] ?? 'Manual stock adjustment',
                ]);
            });

            return back()->withSuccess('Stock adjusted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }
    }
}

