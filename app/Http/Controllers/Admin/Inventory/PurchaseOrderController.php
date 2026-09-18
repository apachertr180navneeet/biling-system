<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryPurchaseOrder;
use App\Models\InventoryPurchaseOrderItem;
use App\Models\InventorySupplier;
use App\Models\InventoryItem;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.inventory.purchase-orders.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = InventoryPurchaseOrder::query()->with(['supplier', 'createdBy']);
        return DataTables::of($query)
            ->filterColumn('po_number', function ($query, $value) {
                $query->where('po_number', 'like', "%{$value}%");
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
            ->addColumn('hotel_name', fn($po) => $po->hotel->name ?? '')
            ->addColumn('supplier_name', fn($po) => $po->supplier->name ?? '')
            ->addColumn('po_status_badge', function ($po) {
                $colors = ['draft' => 'secondary', 'pending' => 'warning', 'approved' => 'info', 'received' => 'success', 'cancelled' => 'danger'];
                $color = $colors[$po->po_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($po->po_status) . '</span>';
            })
            ->addColumn('status_url', fn($po) => route('admin.inventory.purchase-orders.status', $po))
            ->addColumn('edit_url', fn($po) => route('admin.inventory.purchase-orders.edit', $po))
            ->addColumn('delete_url', fn($po) => route('admin.inventory.purchase-orders.destroy', $po))
            ->rawColumns(['po_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $purchaseOrder = null;
        $hotels = Hotel::where('status', 'active')->get();
        $suppliers = InventorySupplier::where('status', 'active')->get();
        $items = InventoryItem::where('status', 'active')->get();
        return view('admin.inventory.purchase-orders.form', compact('purchaseOrder', 'hotels', 'suppliers', 'items'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'supplier_id' => 'required|exists:inventory_suppliers,id',
                'po_date' => 'required|date',
                'expected_delivery_date' => 'nullable|date',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'po_status' => 'required|in:draft,pending,approved,received,cancelled',
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'exists:inventory_items,id',
                'quantities' => 'required|array|min:1',
                'quantities.*' => 'required|numeric|min:1',
                'unit_costs' => 'required|array|min:1',
                'unit_costs.*' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $data = $request->all();
            $data['slug'] = Helper::slug('inventory_purchase_orders', $request->po_date);
            $data['po_number'] = InventoryPurchaseOrder::max('id') + 1;
            $data['po_number'] = 'PO-' . str_pad($data['po_number'], 5, '0', STR_PAD_LEFT);
            $data['created_by'] = auth()->id();

            $purchaseOrder = InventoryPurchaseOrder::create($data);

            foreach ($request->item_ids as $index => $itemId) {
                $quantity = $request->quantities[$index];
                $unitCost = $request->unit_costs[$index];
                InventoryPurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $itemId,
                    'quantity_ordered' => $quantity,
                    'quantity_received' => 0,
                    'unit_cost' => $unitCost,
                    'total_cost' => $quantity * $unitCost,
                    'notes' => null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.inventory.purchase-orders.index')->with('success', 'Purchase Order created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(InventoryPurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        $hotels = Hotel::where('status', 'active')->get();
        $suppliers = InventorySupplier::where('status', 'active')->get();
        $items = InventoryItem::where('status', 'active')->get();
        return view('admin.inventory.purchase-orders.form', compact('purchaseOrder', 'hotels', 'suppliers', 'items'));
    }

    public function update(Request $request, InventoryPurchaseOrder $purchaseOrder)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'supplier_id' => 'required|exists:inventory_suppliers,id',
                'po_date' => 'required|date',
                'expected_delivery_date' => 'nullable|date',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'po_status' => 'required|in:draft,pending,approved,received,cancelled',
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'exists:inventory_items,id',
                'quantities' => 'required|array|min:1',
                'quantities.*' => 'required|numeric|min:1',
                'unit_costs' => 'required|array|min:1',
                'unit_costs.*' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $purchaseOrder->update($request->except(['po_number', 'created_by', 'item_ids', 'quantities', 'unit_costs']));

            $purchaseOrder->items()->delete();

            foreach ($request->item_ids as $index => $itemId) {
                $quantity = $request->quantities[$index];
                $unitCost = $request->unit_costs[$index];
                InventoryPurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $itemId,
                    'quantity_ordered' => $quantity,
                    'quantity_received' => 0,
                    'unit_cost' => $unitCost,
                    'total_cost' => $quantity * $unitCost,
                    'notes' => null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.inventory.purchase-orders.index')->with('success', 'Purchase Order updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, InventoryPurchaseOrder $purchaseOrder)
    {
        try {
            DB::beginTransaction();
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Purchase Order deleted successfully!']);
            }
            return redirect()->route('admin.inventory.purchase-orders.index')->with('success', 'Purchase Order deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, InventoryPurchaseOrder $purchaseOrder)
    {
        try {
            $statusFlow = [
                'draft' => 'pending',
                'pending' => 'approved',
                'approved' => 'received',
            ];
            $current = $purchaseOrder->po_status;
            if (isset($statusFlow[$current])) {
                $purchaseOrder->po_status = $statusFlow[$current];
                $purchaseOrder->save();
            }
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $purchaseOrder->po_status, 'message' => 'Purchase Order status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Purchase Order status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
