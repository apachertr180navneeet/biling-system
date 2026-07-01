<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehiclePurchaseOrder;
use App\Models\VehicleInventory;
use App\Models\Supplier;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehiclePurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = VehiclePurchaseOrder::with('supplier')->withCount('items')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.vehicle_purchase_orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $vehicleOptions = $this->getVehicleOptions();
        return view('admin.vehicle_purchase_orders.create', compact('suppliers', 'vehicleOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.vehicle_description' => 'required|string|max:255',
            'items.*.color_name' => 'nullable|string|max:100',
            'items.*.mfg_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $last = VehiclePurchaseOrder::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $data['po_number'] = 'VPO-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $data['status'] = 'pending';

        $total = 0;
        $items = [];
        foreach ($data['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $total += $lineTotal;
            $items[] = new \App\Models\VehiclePoItem([
                'vehicle_description' => $item['vehicle_description'],
                'color_name' => $item['color_name'],
                'mfg_year' => $item['mfg_year'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $lineTotal,
            ]);
        }
        unset($data['items']);
        $data['total_amount'] = $total;

        $order = DB::transaction(function () use ($data, $items) {
            $order = VehiclePurchaseOrder::create($data);
            $order->items()->saveMany($items);
            return $order;
        });

        return redirect()->route('admin.vehicle-purchase-orders.show', $order)->withSuccess('Vehicle PO created.');
    }

    public function show(VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        $vehiclePurchaseOrder->load('supplier', 'items');
        return view('admin.vehicle_purchase_orders.show', compact('vehiclePurchaseOrder'));
    }

    public function edit(VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        if ($vehiclePurchaseOrder->status !== 'pending') {
            return redirect()->route('admin.vehicle-purchase-orders.index')->with('error', 'Only pending orders can be edited.');
        }
        $vehiclePurchaseOrder->load('items');
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $vehicleOptions = $this->getVehicleOptions();
        return view('admin.vehicle_purchase_orders.edit', compact('vehiclePurchaseOrder', 'suppliers', 'vehicleOptions'));
    }

    public function update(Request $request, VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        if ($vehiclePurchaseOrder->status !== 'pending') {
            return redirect()->route('admin.vehicle-purchase-orders.index')->with('error', 'Only pending orders can be edited.');
        }

        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.vehicle_description' => 'required|string|max:255',
            'items.*.color_name' => 'nullable|string|max:100',
            'items.*.mfg_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $total = 0;
        $newItems = [];
        foreach ($data['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $total += $lineTotal;
            $newItems[] = new \App\Models\VehiclePoItem([
                'vehicle_description' => $item['vehicle_description'],
                'color_name' => $item['color_name'],
                'mfg_year' => $item['mfg_year'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $lineTotal,
            ]);
        }
        unset($data['items']);
        $data['total_amount'] = $total;

        DB::transaction(function () use ($vehiclePurchaseOrder, $data, $newItems) {
            $vehiclePurchaseOrder->update($data);
            $vehiclePurchaseOrder->items()->delete();
            $vehiclePurchaseOrder->items()->saveMany($newItems);
        });

        return redirect()->route('admin.vehicle-purchase-orders.index')->withSuccess('Vehicle PO updated.');
    }

    public function destroy(VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        $vehiclePurchaseOrder->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        $vehiclePurchaseOrder->update(['is_active' => !$vehiclePurchaseOrder->is_active]);
        return response()->json(['success' => true, 'is_active' => $vehiclePurchaseOrder->fresh()->is_active]);
    }

    public function receive(VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        if ($vehiclePurchaseOrder->status === 'received') {
            return redirect()->route('admin.vehicle-purchase-orders.show', $vehiclePurchaseOrder)->with('error', 'Already fully received.');
        }
        $vehiclePurchaseOrder->load('items');
        return view('admin.vehicle_purchase_orders.receive', compact('vehiclePurchaseOrder'));
    }

    public function receiveStore(Request $request, VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        if ($vehiclePurchaseOrder->status === 'received') {
            return back()->with('error', 'Already fully received.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:vehicle_po_items,id',
            'items.*.received_qty' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $vehiclePurchaseOrder) {
            $allFullyReceived = true;
            $anyReceived = false;

            foreach ($request->items as $itemData) {
                $poItem = $vehiclePurchaseOrder->items()->findOrFail($itemData['id']);
                $newReceived = min($itemData['received_qty'], $poItem->quantity);
                $poItem->update(['received_quantity' => $newReceived]);

                if ($newReceived > 0) {
                    $anyReceived = true;
                    $existing = VehicleInventory::where('vehicle_po_id', $vehiclePurchaseOrder->id)
                        ->where('vehicle_description', $poItem->vehicle_description)
                        ->where('color_name', $poItem->color_name)
                        ->where('mfg_year', $poItem->mfg_year)
                        ->where('status', 'available')
                        ->first();
                    if ($existing) {
                        $existing->increment('quantity', $newReceived);
                        $existing->update(['purchase_price' => $poItem->unit_price]);
                    } else {
                        VehicleInventory::create([
                            'vehicle_po_id' => $vehiclePurchaseOrder->id,
                            'vehicle_description' => $poItem->vehicle_description,
                            'color_name' => $poItem->color_name,
                            'mfg_year' => $poItem->mfg_year,
                            'quantity' => $newReceived,
                            'purchase_price' => $poItem->unit_price,
                            'status' => 'available',
                        ]);
                    }
                }

                if ($newReceived < $poItem->quantity) {
                    $allFullyReceived = false;
                }
            }

            if (!$anyReceived) {
                throw new \Exception('Receive at least one item.');
            }

            $vehiclePurchaseOrder->update([
                'status' => $allFullyReceived ? 'received' : 'partial',
            ]);
        });

        return redirect()->route('admin.vehicle-purchase-orders.show', $vehiclePurchaseOrder)->withSuccess('Items received. Inventory updated.');
    }

    public function inventory()
    {
        $inventories = VehicleInventory::where('is_active', true)->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.vehicle_inventories.index', compact('inventories'));
    }

    private function getVehicleOptions(): array
    {
        $options = [];
        VehicleModel::with('brand', 'variants')->whereHas('brand', fn($q) => $q->where('is_active', true))->orderBy('name')->get()->each(function ($model) use (&$options) {
            if ($model->variants->count()) {
                foreach ($model->variants as $variant) {
                    $options[] = $model->brand->name . ' ' . $model->name . ' ' . $variant->name;
                }
            } else {
                $options[] = $model->brand->name . ' ' . $model->name;
            }
        });
        return $options;
    }
}
