<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceSeries;
use App\Models\Customer;
use App\Models\VehicleStock;
use App\Models\SparePart;
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
        $stocks = VehicleStock::with('color.variant.model.brand')
            ->where('status', 'available')
            ->where('is_active', true)
            ->orderBy('chassis_number')->get();
        return view('admin.invoices.create_vehicle', compact('customers', 'stocks'));
    }

    public function storeVehicle(Request $request, GstCalculator $gst)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_stock_id' => 'required|exists:vehicle_stocks,id',
            'invoice_date' => 'required|date',
            'selling_price' => 'required|numeric|min:0',
            'is_gst' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        $stock = VehicleStock::where('id', $data['vehicle_stock_id'])->where('status', 'available')->firstOrFail();
        $customer = Customer::findOrFail($data['customer_id']);

        $result = $gst->calculateForVehicle($data['selling_price'], $data['is_gst'], $customer);

        $invoice = DB::transaction(function () use ($data, $result) {
            $series = InvoiceSeries::where('type', $data['is_gst'] ? 'gst' : 'non_gst')
                ->where('fiscal_year', $this->fiscalYear())
                ->firstOrFail();

            $invoice = Invoice::create([
                'invoice_number' => $series->nextNumber(),
                'invoice_type' => 'vehicle',
                'customer_id' => $data['customer_id'],
                'vehicle_stock_id' => $data['vehicle_stock_id'],
                'invoice_date' => $data['invoice_date'],
                'is_gst' => $data['is_gst'],
                'gst_type' => $result['gstType'],
                'subtotal' => $data['selling_price'],
                'gst_amount' => $result['gstAmount'],
                'cess_amount' => $result['cessAmount'],
                'total_amount' => $result['total'],
                'round_off' => $result['roundOff'],
                'grand_total' => $result['grandTotal'],
                'status' => 'confirmed',
                'notes' => $data['notes'] ?? null,
            ]);

            VehicleStock::where('id', $data['vehicle_stock_id'])->update(['status' => 'sold']);

            return $invoice;
        });

        return redirect()->route('admin.invoices.show', $invoice)->withSuccess('Vehicle invoice created successfully.');
    }

    public function createParts()
    {
        $customers = Customer::orderBy('first_name')->get();
        $spareParts = SparePart::with('category')->orderBy('name')->get();
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
                ->firstOrFail();

            $invoice = Invoice::create([
                'invoice_number' => $series->nextNumber(),
                'invoice_type' => 'parts',
                'customer_id' => $data['customer_id'],
                'vehicle_stock_id' => null,
                'invoice_date' => $data['invoice_date'],
                'is_gst' => $data['is_gst'],
                'gst_type' => $result['gstType'],
                'subtotal' => $result['subtotal'],
                'gst_amount' => $result['totalGst'],
                'cess_amount' => $result['totalCess'],
                'total_amount' => $result['totalAmount'],
                'round_off' => $result['roundOff'],
                'grand_total' => $result['grandTotal'],
                'status' => 'confirmed',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($result['calculatedItems'] as $item) {
                $invoice->items()->create($item);
            }

            return $invoice;
        });

        return redirect()->route('admin.invoices.show', $invoice)->withSuccess('Parts invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'vehicleStock.color.variant.model.brand', 'items.sparePart.category');
        return view('admin.invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->vehicle_stock_id && $invoice->status === 'confirmed') {
            VehicleStock::where('id', $invoice->vehicle_stock_id)->update(['status' => 'available']);
        }
        $invoice->delete();
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
