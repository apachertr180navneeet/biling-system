<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryStockAdjustment;
use App\Models\InventoryStock;
use App\Models\InventoryItem;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.inventory.stock-adjustments.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = InventoryStockAdjustment::query()->with(['item', 'createdBy', 'approvedBy']);

        return DataTables::of($query)
            ->filterColumn('adjustment_number', function ($query, $value) {
                $query->where('adjustment_number', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('item_name', function ($query, $value) {
                $query->whereHas('item', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('adjustment_no', fn($a) => $a->adjustment_number ?? '')
            ->addColumn('hotel_name', fn($a) => $a->hotel->name ?? '')
            ->addColumn('item_name', fn($a) => $a->item->name ?? '')
            ->addColumn('adjustment_type_badge', function ($row) {
                $colors = ['addition' => 'success', 'subtraction' => 'warning', 'damage' => 'danger', 'expired' => 'info', 'theft' => 'danger'];
                $color = $colors[$row->adjustment_type] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->adjustment_type) . '</span>';
            })
            ->addColumn('status_url', fn($a) => route('admin.inventory.stock-adjustments.status', $a))
            ->addColumn('edit_url', fn($a) => route('admin.inventory.stock-adjustments.edit', $a))
            ->addColumn('delete_url', fn($a) => route('admin.inventory.stock-adjustments.destroy', $a))
            ->rawColumns(['adjustment_type_badge'])
            ->make(true);
    }

    public function create()
    {
        $stockAdjustment = null;
        $hotels = Hotel::where('status', 'active')->get();
        $items = InventoryItem::where('status', 'active')->get();
        return view('admin.inventory.stock-adjustments.form', compact('stockAdjustment', 'hotels', 'items'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'item_id' => 'required|exists:inventory_items,id',
                'adjustment_date' => 'required|date',
                'adjustment_type' => 'required|in:addition,subtraction,damage,expired,theft',
                'quantity_before' => 'required|integer|min:0',
                'adjustment_quantity' => 'required|integer|min:1',
                'unit_cost' => 'required|numeric|min:0',
                'reason' => 'required|string',
            ]);

            $adjustmentType = $request->adjustment_type;
            $quantityBefore = $request->quantity_before;
            $adjustmentQuantity = $request->adjustment_quantity;

            if ($adjustmentType === 'addition') {
                $quantityAfter = $quantityBefore + $adjustmentQuantity;
            } else {
                $quantityAfter = $quantityBefore - $adjustmentQuantity;
            }

            $totalValue = $adjustmentQuantity * $request->unit_cost;

            $data = $request->all();
            $data['adjustment_number'] = 'SA-' . date('YmdHis');
            $data['slug'] = Helper::slug('inventory_stock_adjustments', $data['adjustment_number']);
            $data['quantity_after'] = $quantityAfter;
            $data['total_value'] = $totalValue;
            $data['created_by'] = auth()->id();

            InventoryStockAdjustment::create(collect($data)->only([
                'hotel_id', 'item_id', 'adjustment_number', 'slug', 'adjustment_date',
                'adjustment_type', 'quantity_before', 'adjustment_quantity', 'quantity_after',
                'unit_cost', 'total_value', 'reason', 'created_by',
            ])->toArray());

            return redirect()->route('admin.inventory.stock-adjustments.index')->with('success', 'Stock Adjustment created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(InventoryStockAdjustment $stockAdjustment)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $items = InventoryItem::where('status', 'active')->get();
        return view('admin.inventory.stock-adjustments.form', compact('stockAdjustment', 'hotels', 'items'));
    }

    public function update(Request $request, InventoryStockAdjustment $stockAdjustment)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'item_id' => 'required|exists:inventory_items,id',
                'adjustment_date' => 'required|date',
                'adjustment_type' => 'required|in:addition,subtraction,damage,expired,theft',
                'quantity_before' => 'required|integer|min:0',
                'adjustment_quantity' => 'required|integer|min:1',
                'unit_cost' => 'required|numeric|min:0',
                'reason' => 'required|string',
            ]);

            $adjustmentType = $request->adjustment_type;
            $quantityBefore = $request->quantity_before;
            $adjustmentQuantity = $request->adjustment_quantity;

            if ($adjustmentType === 'addition') {
                $quantityAfter = $quantityBefore + $adjustmentQuantity;
            } else {
                $quantityAfter = $quantityBefore - $adjustmentQuantity;
            }

            $totalValue = $adjustmentQuantity * $request->unit_cost;

            $stockAdjustment->update(collect($request->all())->only([
                'hotel_id', 'item_id', 'adjustment_date', 'adjustment_type',
                'quantity_before', 'adjustment_quantity', 'unit_cost', 'reason',
            ])->toArray());

            $stockAdjustment->update([
                'quantity_after' => $quantityAfter,
                'total_value' => $totalValue,
            ]);

            return redirect()->route('admin.inventory.stock-adjustments.index')->with('success', 'Stock Adjustment updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, InventoryStockAdjustment $stockAdjustment)
    {
        try {
            $stockAdjustment->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Stock Adjustment deleted successfully!']);
            }
            return redirect()->route('admin.inventory.stock-adjustments.index')->with('success', 'Stock Adjustment deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, InventoryStockAdjustment $stockAdjustment)
    {
        try {
            $stockAdjustment->status = $stockAdjustment->status === 'active' ? 'inactive' : 'active';
            $stockAdjustment->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $stockAdjustment->status, 'message' => 'Status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
