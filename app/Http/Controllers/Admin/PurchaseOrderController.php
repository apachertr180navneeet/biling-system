<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\SparePart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::with('supplier', 'items')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.purchase_orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $spareParts = SparePart::with('category')->orderBy('name')->get();
        return view('admin.purchase_orders.create', compact('suppliers', 'spareParts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'required|exists:spare_parts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $lastOrder = PurchaseOrder::orderBy('id', 'desc')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $data['order_number'] = 'PO-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $data['created_by'] = Auth::id();

        $total = 0;
        $items = [];
        foreach ($data['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $total += $lineTotal;
            $items[] = new PurchaseOrderItem([
                'spare_part_id' => $item['spare_part_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $lineTotal,
            ]);
        }
        $data['total_amount'] = $total;
        unset($data['items']);

        $order = PurchaseOrder::create($data);
        $order->items()->saveMany($items);

        return redirect()->route('admin.purchase-orders.index')->withSuccess('Purchase order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.sparePart.category', 'createdBy');
        return view('admin.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('admin.purchase-orders.index')->with('error', 'Only pending orders can be edited.');
        }
        $purchaseOrder->load('items');
        $suppliers = Supplier::orderBy('name')->get();
        $spareParts = SparePart::with('category')->orderBy('name')->get();
        return view('admin.purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'spareParts'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('admin.purchase-orders.index')->with('error', 'Only pending orders can be edited.');
        }

        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'required|exists:spare_parts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $total = 0;
        $newItems = [];
        foreach ($data['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $total += $lineTotal;
            $newItems[] = new PurchaseOrderItem([
                'spare_part_id' => $item['spare_part_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $lineTotal,
            ]);
        }
        unset($data['items']);
        $data['total_amount'] = $total;

        $purchaseOrder->update($data);
        $purchaseOrder->items()->delete();
        $purchaseOrder->items()->saveMany($newItems);

        return redirect()->route('admin.purchase-orders.index')->withSuccess('Purchase order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return response()->json(['success' => true, 'message' => 'Purchase order deleted successfully.']);
    }

    public function toggleStatus(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update(['is_active' => !$purchaseOrder->is_active]);
        return response()->json(['success' => true, 'is_active' => $purchaseOrder->fresh()->is_active]);
    }
}
