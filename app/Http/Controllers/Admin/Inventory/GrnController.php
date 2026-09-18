<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryGrn;
use App\Models\InventoryGrnItem;
use App\Models\InventoryPurchaseOrder;
use App\Models\InventoryPurchaseOrderItem;
use App\Models\InventorySupplier;
use App\Models\InventoryItem;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use DB;

class GrnController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.inventory.grns.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = InventoryGrn::query()->with(['supplier', 'purchaseOrder', 'createdBy']);
        return DataTables::of($query)
            ->filterColumn('grn_number', function ($query, $value) {
                $query->where('grn_number', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('supplier_name', function ($query, $value) {
                $query->whereHas('supplier', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($grn) => $grn->hotel->name ?? '')
            ->addColumn('supplier_name', fn($grn) => $grn->supplier->name ?? '')
            ->addColumn('po_number', fn($grn) => $grn->purchaseOrder->po_number ?? '')
            ->addColumn('grn_status_badge', function ($grn) {
                $colors = ['pending' => 'warning', 'partial' => 'info', 'completed' => 'success', 'rejected' => 'danger'];
                $color = $colors[$grn->grn_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($grn->grn_status) . '</span>';
            })
            ->addColumn('status_url', fn($grn) => route('admin.inventory.grns.status', $grn))
            ->addColumn('edit_url', fn($grn) => route('admin.inventory.grns.edit', $grn))
            ->addColumn('delete_url', fn($grn) => route('admin.inventory.grns.destroy', $grn))
            ->rawColumns(['grn_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $grn = null;
        $hotels = Hotel::where('status', 'active')->get();
        $suppliers = InventorySupplier::where('status', 'active')->get();
        $purchaseOrders = InventoryPurchaseOrder::where('po_status', 'approved')->get();
        $items = InventoryItem::where('status', 'active')->get();
        return view('admin.inventory.grns.form', compact('grn', 'hotels', 'suppliers', 'purchaseOrders', 'items'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'purchase_order_id' => 'required|exists:inventory_purchase_orders,id',
                'supplier_id' => 'required|exists:inventory_suppliers,id',
                'grn_date' => 'required|date',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'remarks' => 'nullable|string',
                'grn_status' => 'required|in:pending,partial,completed,rejected',
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'exists:inventory_items,id',
                'quantities_ordered' => 'required|array|min:1',
                'quantities_ordered.*' => 'required|numeric|min:0',
                'quantities_received' => 'required|array|min:1',
                'quantities_received.*' => 'required|numeric|min:0',
                'quantities_accepted' => 'required|array|min:1',
                'quantities_accepted.*' => 'required|numeric|min:0',
                'quantities_rejected' => 'required|array|min:1',
                'quantities_rejected.*' => 'required|numeric|min:0',
                'unit_costs' => 'required|array|min:1',
                'unit_costs.*' => 'required|numeric|min:0',
                'rejection_reasons' => 'nullable|array',
            ]);

            DB::beginTransaction();

            $data = $request->all();
            $data['slug'] = Helper::slug('inventory_grns', $request->grn_date);
            $data['grn_number'] = InventoryGrn::max('id') + 1;
            $data['grn_number'] = 'GRN-' . str_pad($data['grn_number'], 5, '0', STR_PAD_LEFT);
            $data['received_by'] = auth()->id();
            $data['created_by'] = auth()->id();

            $grn = InventoryGrn::create($data);

            foreach ($request->item_ids as $index => $itemId) {
                $quantityReceived = $request->quantities_received[$index];
                $quantityAccepted = $request->quantities_accepted[$index];
                $quantityRejected = $request->quantities_rejected[$index];
                $unitCost = $request->unit_costs[$index];

                InventoryGrnItem::create([
                    'grn_id' => $grn->id,
                    'item_id' => $itemId,
                    'quantity_ordered' => $request->quantities_ordered[$index],
                    'quantity_received' => $quantityReceived,
                    'quantity_accepted' => $quantityAccepted,
                    'quantity_rejected' => $quantityRejected,
                    'unit_cost' => $unitCost,
                    'total_cost' => $quantityAccepted * $unitCost,
                    'rejection_reason' => $request->rejection_reasons[$index] ?? null,
                ]);

                InventoryPurchaseOrderItem::where('purchase_order_id', $request->purchase_order_id)
                    ->where('item_id', $itemId)
                    ->increment('quantity_received', $quantityAccepted);
            }

            DB::commit();

            return redirect()->route('admin.inventory.grns.index')->with('success', 'GRN created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(InventoryGrn $grn)
    {
        $grn->load('items');
        $hotels = Hotel::where('status', 'active')->get();
        $suppliers = InventorySupplier::where('status', 'active')->get();
        $purchaseOrders = InventoryPurchaseOrder::whereIn('po_status', ['approved', 'received'])->get();
        $items = InventoryItem::where('status', 'active')->get();
        return view('admin.inventory.grns.form', compact('grn', 'hotels', 'suppliers', 'purchaseOrders', 'items'));
    }

    public function update(Request $request, InventoryGrn $grn)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'purchase_order_id' => 'required|exists:inventory_purchase_orders,id',
                'supplier_id' => 'required|exists:inventory_suppliers,id',
                'grn_date' => 'required|date',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'remarks' => 'nullable|string',
                'grn_status' => 'required|in:pending,partial,completed,rejected',
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'exists:inventory_items,id',
                'quantities_ordered' => 'required|array|min:1',
                'quantities_ordered.*' => 'required|numeric|min:0',
                'quantities_received' => 'required|array|min:1',
                'quantities_received.*' => 'required|numeric|min:0',
                'quantities_accepted' => 'required|array|min:1',
                'quantities_accepted.*' => 'required|numeric|min:0',
                'quantities_rejected' => 'required|array|min:1',
                'quantities_rejected.*' => 'required|numeric|min:0',
                'unit_costs' => 'required|array|min:1',
                'unit_costs.*' => 'required|numeric|min:0',
                'rejection_reasons' => 'nullable|array',
            ]);

            DB::beginTransaction();

            foreach ($grn->items as $oldItem) {
                InventoryPurchaseOrderItem::where('purchase_order_id', $grn->purchase_order_id)
                    ->where('item_id', $oldItem->item_id)
                    ->decrement('quantity_received', $oldItem->quantity_accepted);
            }

            $grn->update($request->except(['grn_number', 'received_by', 'created_by', 'item_ids', 'quantities_ordered', 'quantities_received', 'quantities_accepted', 'quantities_rejected', 'unit_costs', 'rejection_reasons']));

            $grn->items()->delete();

            foreach ($request->item_ids as $index => $itemId) {
                $quantityAccepted = $request->quantities_accepted[$index];
                $quantityRejected = $request->quantities_rejected[$index];
                $unitCost = $request->unit_costs[$index];

                InventoryGrnItem::create([
                    'grn_id' => $grn->id,
                    'item_id' => $itemId,
                    'quantity_ordered' => $request->quantities_ordered[$index],
                    'quantity_received' => $request->quantities_received[$index],
                    'quantity_accepted' => $quantityAccepted,
                    'quantity_rejected' => $quantityRejected,
                    'unit_cost' => $unitCost,
                    'total_cost' => $quantityAccepted * $unitCost,
                    'rejection_reason' => $request->rejection_reasons[$index] ?? null,
                ]);

                InventoryPurchaseOrderItem::where('purchase_order_id', $request->purchase_order_id)
                    ->where('item_id', $itemId)
                    ->increment('quantity_received', $quantityAccepted);
            }

            DB::commit();

            return redirect()->route('admin.inventory.grns.index')->with('success', 'GRN updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, InventoryGrn $grn)
    {
        try {
            DB::beginTransaction();

            foreach ($grn->items as $grnItem) {
                InventoryPurchaseOrderItem::where('purchase_order_id', $grn->purchase_order_id)
                    ->where('item_id', $grnItem->item_id)
                    ->decrement('quantity_received', $grnItem->quantity_accepted);
            }

            $grn->items()->delete();
            $grn->delete();

            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'GRN deleted successfully!']);
            }
            return redirect()->route('admin.inventory.grns.index')->with('success', 'GRN deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, InventoryGrn $grn)
    {
        try {
            $statusFlow = [
                'pending' => 'partial',
                'partial' => 'completed',
            ];
            $current = $grn->grn_status;
            if (isset($statusFlow[$current])) {
                $grn->grn_status = $statusFlow[$current];
                $grn->save();
            }
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $grn->grn_status, 'message' => 'GRN status updated successfully!']);
            }
            return redirect()->back()->with('success', 'GRN status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
