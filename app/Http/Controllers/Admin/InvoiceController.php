<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceSeries;
use App\Models\Customer;
use App\Models\SparePart;
use App\Models\SparePartStock;
use App\Models\VehicleInventory;
use App\Models\VehicleMaster;
use App\Models\Payment;
use App\Services\GstCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer', 'items')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function createVehicle()
    {
        $customers = Customer::orderBy('first_name')->get();
        $vehicleInventories = VehicleInventory::where('quantity', '>', 0)->where('status', 'available')->where('is_active', true)->orderBy('vehicle_description')->get();
        $vehiclePrices = [];
        VehicleMaster::where('is_active', true)->get()->each(function ($v) use (&$vehiclePrices) {
            $desc = trim($v->variant_name . ' ' . $v->color_name);
            if ($v->ex_showroom_price) {
                $vehiclePrices[$desc] = $v->ex_showroom_price;
            }
        });
        return view('admin.invoices.create_vehicle', compact('customers', 'vehicleInventories', 'vehiclePrices'));
    }

    public function storeVehicle(Request $request, GstCalculator $gst)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_inventory_id' => 'nullable|exists:vehicle_inventories,id',
            'vehicle_description' => 'required|string|max:255',
            'chassis_number' => 'nullable|string|max:255',
            'engine_number' => 'nullable|string|max:255',
            'mfg_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'invoice_date' => 'required|date',
            'selling_price' => 'required|numeric|min:0',
            'is_gst' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::findOrFail($data['customer_id']);

        $result = $gst->calculateForVehicle($data['selling_price'], $data['is_gst'], $customer);

        $invoice = DB::transaction(function () use ($data, $result) {
            $series = InvoiceSeries::where('type', $data['is_gst'] ? 'gst' : 'non_gst')
                ->where('fiscal_year', $this->fiscalYear())
                ->lockForUpdate()
                ->firstOrFail();

            if (!empty($data['vehicle_inventory_id'])) {
                $inv = VehicleInventory::where('id', $data['vehicle_inventory_id'])->lockForUpdate()->firstOrFail();
                if ($inv->quantity < 1 || $inv->status !== 'available') {
                    throw new \Exception('Vehicle inventory not available.');
                }
                $inv->decrement('quantity');
                if ($inv->fresh()->quantity < 1) {
                    $inv->update(['status' => 'sold']);
                }
            }

            $invoice = Invoice::create([
                'invoice_number' => $series->nextNumber(),
                'invoice_type' => 'vehicle',
                'customer_id' => $data['customer_id'],
                'vehicle_inventory_id' => $data['vehicle_inventory_id'] ?? null,
                'vehicle_description' => $data['vehicle_description'],
                'chassis_number' => $data['chassis_number'],
                'engine_number' => $data['engine_number'],
                'mfg_year' => $data['mfg_year'],
                'invoice_date' => $data['invoice_date'],
                'is_gst' => $data['is_gst'],
                'gst_type' => $result['gstType'],
                'subtotal' => $data['selling_price'],
                'gst_amount' => $result['gstAmount'],
                'cgst_amount' => $result['cgstAmount'],
                'sgst_amount' => $result['sgstAmount'],
                'igst_amount' => $result['igstAmount'],
                'cess_amount' => $result['cessAmount'],
                'total_amount' => $result['total'],
                'round_off' => $result['roundOff'],
                'grand_total' => $result['grandTotal'],
                'status' => 'confirmed',
                'notes' => $data['notes'] ?? null,
            ]);

            return $invoice;
        });

        return redirect()->route('admin.invoices.show', $invoice)->withSuccess('Vehicle invoice created successfully.');
    }

    public function createParts()
    {
        $customers = Customer::orderBy('first_name')->get();
        $spareParts = SparePart::orderBy('name')->get();
        return view('admin.invoices.create_parts', compact('customers', 'spareParts'));
    }

    public function storeParts(Request $request, GstCalculator $gst)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'is_gst' => 'required|boolean',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'nullable|exists:spare_parts,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($data['customer_id']);

        $result = $gst->calculateForItems($data['items'], $data['is_gst'], $customer);

        $invoice = DB::transaction(function () use ($data, $result) {
            $series = InvoiceSeries::where('type', $data['is_gst'] ? 'gst' : 'non_gst')
                ->where('fiscal_year', $this->fiscalYear())
                ->lockForUpdate()
                ->firstOrFail();

            $invoice = Invoice::create([
                'invoice_number' => $series->nextNumber(),
                'invoice_type' => 'parts',
                'customer_id' => $data['customer_id'],
                'invoice_date' => $data['invoice_date'],
                'is_gst' => $data['is_gst'],
                'gst_type' => $result['gstType'],
                'subtotal' => $result['subtotal'],
                'gst_amount' => $result['totalGst'],
                'cgst_amount' => $result['totalCgst'],
                'sgst_amount' => $result['totalSgst'],
                'igst_amount' => $result['totalIgst'],
                'cess_amount' => $result['totalCess'],
                'total_amount' => $result['totalAmount'],
                'round_off' => $result['roundOff'],
                'grand_total' => $result['grandTotal'],
                'status' => 'confirmed',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($result['calculatedItems'] as $item) {
                $invoice->items()->create($item);
                if (!empty($item['spare_part_id'])) {
                    $stock = SparePartStock::where('spare_part_id', $item['spare_part_id'])->lockForUpdate()->first();
                    if (!$stock || $stock->quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for spare part ID {$item['spare_part_id']}. Available: " . ($stock->quantity ?? 0) . ", requested: {$item['quantity']}.");
                    }
                    $stock->decrement('quantity', $item['quantity']);
                }
            }

            return $invoice;
        });

        return redirect()->route('admin.invoices.show', $invoice)->withSuccess('Parts invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items.sparePart.category');
        return view('admin.invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            if ($invoice->invoice_type === 'vehicle' && $invoice->vehicle_inventory_id) {
                $inv = VehicleInventory::find($invoice->vehicle_inventory_id);
                if ($inv) {
                    $inv->increment('quantity');
                    $inv->update(['status' => 'available']);
                }
            }
            if ($invoice->invoice_type === 'parts') {
                $invoice->load('items');
                foreach ($invoice->items as $item) {
                    if ($item->spare_part_id) {
                        $stock = SparePartStock::firstOrCreate(
                            ['spare_part_id' => $item->spare_part_id],
                            ['quantity' => 0, 'min_quantity' => 0, 'purchase_price' => 0]
                        );
                        $stock->increment('quantity', $item->quantity);
                    }
                }
            }
            Payment::where('invoice_id', $invoice->id)->update(['invoice_id' => null]);
            $invoice->delete();
        });
        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully.']);
    }

    private function fiscalYear(): string
    {
        $y = date('Y');
        $m = date('m');
        if ($m >= 4) {
            return $y . '-' . substr($y + 1, -2);
        }
        return ($y - 1) . '-' . substr($y, -2);
    }
}
