<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehiclePurchaseOrder;
use App\Models\VehicleInventory;
use App\Models\Supplier;
use App\Models\VehicleMaster;
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
        $vehicleData = $this->getVehicleOptions();
        $vehicleList = $vehicleData['list'];
        $vehiclePrices = $vehicleData['prices'];
        return view('admin.vehicle_purchase_orders.create', compact('suppliers', 'vehicleList', 'vehiclePrices'));
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
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $data['status'] = 'pending';

        $total = 0;
        $items = [];
        foreach ($data['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $total += $lineTotal;
            $items[] = new \App\Models\VehiclePoItem([
                'vehicle_description' => $item['vehicle_description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $lineTotal,
            ]);
        }
        unset($data['items']);
        $data['total_amount'] = $total;

        $order = DB::transaction(function () use ($data, $items) {
            $last = DB::table('vehicle_purchase_orders')->lockForUpdate()->orderBy('id', 'desc')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $data['po_number'] = 'VPO-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
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
        $vehicleData = $this->getVehicleOptions();
        $vehicleList = $vehicleData['list'];
        $vehiclePrices = $vehicleData['prices'];
        return view('admin.vehicle_purchase_orders.edit', compact('vehiclePurchaseOrder', 'suppliers', 'vehicleList', 'vehiclePrices'));
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
        DB::transaction(function () use ($vehiclePurchaseOrder) {
            $vehiclePurchaseOrder->load('items');
            foreach ($vehiclePurchaseOrder->items as $item) {
                if ($item->received_quantity > 0) {
                    $existing = VehicleInventory::where('vehicle_po_id', $vehiclePurchaseOrder->id)
                        ->where('vehicle_description', $item->vehicle_description)
                        ->where('color_name', $item->color_name)
                        ->where('mfg_year', $item->mfg_year)
                        ->where('status', 'available')
                        ->lockForUpdate()
                        ->first();
                    if ($existing) {
                        $existing->decrement('quantity', $item->received_quantity);
                        if ($existing->quantity <= 0) {
                            $existing->update(['status' => 'sold']);
                        }
                    }
                }
            }
            $vehiclePurchaseOrder->items()->delete();
            $vehiclePurchaseOrder->delete();
        });
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        $vehiclePurchaseOrder->update(['is_active' => !$vehiclePurchaseOrder->is_active]);
        $vehiclePurchaseOrder->refresh();
        return response()->json(['success' => true, 'is_active' => $vehiclePurchaseOrder->is_active]);
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
                $previousReceived = $poItem->received_quantity;
                $newReceived = min($itemData['received_qty'] + $previousReceived, $poItem->quantity);
                $delta = $newReceived - $previousReceived;
                $poItem->update(['received_quantity' => $newReceived]);

                if ($delta > 0) {
                    $anyReceived = true;
                    $existing = VehicleInventory::where('vehicle_po_id', $vehiclePurchaseOrder->id)
                        ->where('vehicle_description', $poItem->vehicle_description)
                        ->where('color_name', $poItem->color_name)
                        ->where('mfg_year', $poItem->mfg_year)
                        ->where('status', 'available')
                        ->lockForUpdate()
                        ->first();
                    if ($existing) {
                        $existing->increment('quantity', $delta);
                        $existing->update(['purchase_price' => $poItem->unit_price]);
                    } else {
                        VehicleInventory::create([
                            'vehicle_po_id' => $vehiclePurchaseOrder->id,
                            'vehicle_description' => $poItem->vehicle_description,
                            'color_name' => $poItem->color_name,
                            'mfg_year' => $poItem->mfg_year,
                            'quantity' => $delta,
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
        $list = [];
        $prices = [];
        VehicleMaster::where('is_active', true)->orderBy('variant_name')->get()->each(function ($v) use (&$list, &$prices) {
            $desc = trim($v->variant_name . ' ' . $v->color_name);
            $list[] = $desc;
            if ($v->ex_showroom_price) {
                $prices[$desc] = $v->ex_showroom_price;
            }
        });
        return ['list' => $list, 'prices' => $prices];
    }
}
