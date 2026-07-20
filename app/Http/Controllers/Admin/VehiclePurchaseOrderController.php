<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehiclePurchaseOrder;
use App\Models\VehicleInventory;
use App\Models\VehiclePoItem;
use App\Models\Supplier;
use App\Models\VehicleMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            $items[] = new VehiclePoItem([
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
            $newItems[] = new VehiclePoItem([
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
            VehicleInventory::where('vehicle_po_id', $vehiclePurchaseOrder->id)
                ->where('status', 'available')
                ->update(['status' => 'sold']);
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
        $receivedVehicles = VehicleInventory::where('vehicle_po_id', $vehiclePurchaseOrder->id)->get();
        return view('admin.vehicle_purchase_orders.receive', compact('vehiclePurchaseOrder', 'receivedVehicles'));
    }

    public function receiveStore(Request $request, VehiclePurchaseOrder $vehiclePurchaseOrder)
    {
        if ($vehiclePurchaseOrder->status === 'received') {
            return back()->with('error', 'Already fully received.');
        }

        $request->validate([
            'items' => 'nullable|array',
            'items.*.id' => 'required_with:items|exists:vehicle_po_items,id',
            'items.*.vehicles' => 'nullable|array',
            'edit_vehicles' => 'nullable|array',
            'edit_vehicles.*.id' => 'required_with:edit_vehicles|exists:vehicle_inventories,id',
            'edit_vehicles.*.chassis_number' => 'required_with:edit_vehicles|string|max:255',
            'edit_vehicles.*.engine_number' => 'required_with:edit_vehicles|string|max:255',
            'delete_vehicles' => 'nullable|array',
            'delete_vehicles.*' => 'exists:vehicle_inventories,id',
        ]);

        $allChassis = [];
        $allEngines = [];

        // 1. Validate edited vehicles (exclude any marked for deletion)
        $deletedIds = $request->input('delete_vehicles', []);
        if ($request->has('edit_vehicles')) {
            foreach ($request->edit_vehicles as $id => $vehicle) {
                if (in_array($id, $deletedIds)) {
                    continue;
                }
                if (!empty($vehicle['chassis_number']) && !empty($vehicle['engine_number']) && $vehicle['chassis_number'] === $vehicle['engine_number']) {
                    return back()->withErrors(["edit_vehicles.{$id}.engine_number" => "Chassis number and engine number must be different."])->withInput();
                }
                if (in_array($vehicle['chassis_number'], $allChassis)) {
                    return back()->withErrors(["edit_vehicles.{$id}.chassis_number" => "Duplicate chassis number: {$vehicle['chassis_number']}."])->withInput();
                }
                if (in_array($vehicle['engine_number'], $allEngines)) {
                    return back()->withErrors(["edit_vehicles.{$id}.engine_number" => "Duplicate engine number: {$vehicle['engine_number']}."])->withInput();
                }

                // Database unique check ignoring current ID
                $chassisExists = VehicleInventory::where('chassis_number', $vehicle['chassis_number'])->where('id', '!=', $id)->exists();
                if ($chassisExists) {
                    return back()->withErrors(["edit_vehicles.{$id}.chassis_number" => "Chassis number is already taken."])->withInput();
                }
                $engineExists = VehicleInventory::where('engine_number', $vehicle['engine_number'])->where('id', '!=', $id)->exists();
                if ($engineExists) {
                    return back()->withErrors(["edit_vehicles.{$id}.engine_number" => "Engine number is already taken."])->withInput();
                }

                $allChassis[] = $vehicle['chassis_number'];
                $allEngines[] = $vehicle['engine_number'];
            }
        }

        // 2. Validate new vehicles
        if ($request->has('items')) {
            foreach ($request->items as $itemIdx => $itemData) {
                if (isset($itemData['vehicles'])) {
                    foreach ($itemData['vehicles'] as $vehIdx => $vehicle) {
                        if (empty($vehicle['chassis_number']) || empty($vehicle['engine_number'])) {
                            continue;
                        }
                        if ($vehicle['chassis_number'] === $vehicle['engine_number']) {
                            return back()->withErrors(["items.{$itemIdx}.vehicles.{$vehIdx}.engine_number" => "Chassis number and engine number must be different."])->withInput();
                        }
                        if (in_array($vehicle['chassis_number'], $allChassis)) {
                            return back()->withErrors(["items.{$itemIdx}.vehicles.{$vehIdx}.chassis_number" => "Duplicate chassis number: {$vehicle['chassis_number']}."])->withInput();
                        }
                        if (in_array($vehicle['engine_number'], $allEngines)) {
                            return back()->withErrors(["items.{$itemIdx}.vehicles.{$vehIdx}.engine_number" => "Duplicate engine number: {$vehicle['engine_number']}."])->withInput();
                        }

                        // DB unique check for new ones
                        $chassisExists = VehicleInventory::where('chassis_number', $vehicle['chassis_number'])->exists();
                        if ($chassisExists) {
                            return back()->withErrors(["items.{$itemIdx}.vehicles.{$vehIdx}.chassis_number" => "Chassis number is already taken."])->withInput();
                        }
                        $engineExists = VehicleInventory::where('engine_number', $vehicle['engine_number'])->exists();
                        if ($engineExists) {
                            return back()->withErrors(["items.{$itemIdx}.vehicles.{$vehIdx}.engine_number" => "Engine number is already taken."])->withInput();
                        }

                        $allChassis[] = $vehicle['chassis_number'];
                        $allEngines[] = $vehicle['engine_number'];
                    }
                }
            }
        }

        // Check if anything was actually submitted
        $hasNew = false;
        if ($request->has('items')) {
            foreach ($request->items as $itemData) {
                if (!empty($itemData['vehicles'])) {
                    $hasNew = true;
                    break;
                }
            }
        }
        $hasEdit = false;
        if ($request->has('edit_vehicles')) {
            foreach ($request->edit_vehicles as $id => $val) {
                if (!in_array($id, $deletedIds)) {
                    $hasEdit = true;
                    break;
                }
            }
        }
        $hasDelete = !empty($deletedIds);

        if (!$hasNew && !$hasEdit && !$hasDelete) {
            return back()->with('error', 'No vehicle details were submitted.')->withInput();
        }

        DB::transaction(function () use ($request, $vehiclePurchaseOrder, $hasNew, $hasEdit, $hasDelete, $deletedIds) {
            // Delete removed vehicles and decrement received_quantity
            if ($hasDelete) {
                foreach ($deletedIds as $id) {
                    $vehicle = VehicleInventory::find($id);
                    if ($vehicle) {
                        $poItem = $vehiclePurchaseOrder->items()
                            ->where('vehicle_description', $vehicle->vehicle_description)
                            ->where('color_name', $vehicle->color_name)
                            ->where('mfg_year', $vehicle->mfg_year)
                            ->first();
                        if ($poItem) {
                            $poItem->decrement('received_quantity');
                        }
                        $vehicle->delete();
                    }
                }
            }

            // Update edited vehicles
            if ($hasEdit) {
                foreach ($request->edit_vehicles as $id => $val) {
                    if (in_array($id, $deletedIds)) {
                        continue;
                    }
                    VehicleInventory::where('id', $id)->update([
                        'chassis_number' => $val['chassis_number'],
                        'engine_number' => $val['engine_number'],
                    ]);
                }
            }

            // Create new vehicles and update PO items
            $allFullyReceived = true;
            foreach ($vehiclePurchaseOrder->items()->get() as $poItem) {
                $previousReceived = $poItem->received_quantity;
                $delta = 0;

                // Find matching item index in request
                if ($request->has('items')) {
                    foreach ($request->items as $itemData) {
                        if ((int)$itemData['id'] === (int)$poItem->id && !empty($itemData['vehicles'])) {
                            $delta = count($itemData['vehicles']);
                            foreach ($itemData['vehicles'] as $vehicle) {
                                VehicleInventory::create([
                                    'vehicle_po_id' => $vehiclePurchaseOrder->id,
                                    'vehicle_description' => $poItem->vehicle_description,
                                    'color_name' => $poItem->color_name,
                                    'mfg_year' => $poItem->mfg_year,
                                    'chassis_number' => $vehicle['chassis_number'],
                                    'engine_number' => $vehicle['engine_number'],
                                    'quantity' => 1,
                                    'purchase_price' => $poItem->unit_price,
                                    'status' => 'available',
                                ]);
                            }
                        }
                    }
                }

                $newReceived = min($delta + $previousReceived, $poItem->quantity);
                $poItem->update(['received_quantity' => $newReceived]);

                if ($newReceived < $poItem->quantity) {
                    $allFullyReceived = false;
                }
            }

            $vehiclePurchaseOrder->update([
                'status' => $allFullyReceived ? 'received' : ($vehiclePurchaseOrder->items()->where('received_quantity', '>', 0)->exists() ? 'partial' : 'pending'),
            ]);
        });

        return redirect()->route('admin.vehicle-purchase-orders.show', $vehiclePurchaseOrder)->withSuccess('Vehicles updated successfully.');
    }

    public function checkUnique(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $ignoreId = $request->input('ignore_id');

        if (!in_array($field, ['chassis_number', 'engine_number'])) {
            return response()->json(['valid' => false, 'message' => 'Invalid field.']);
        }

        $query = VehicleInventory::where($field, $value);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        $exists = $query->exists();

        return response()->json([
            'valid' => !$exists,
            'message' => $exists ? "This {$field} is already taken." : '',
        ]);
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
