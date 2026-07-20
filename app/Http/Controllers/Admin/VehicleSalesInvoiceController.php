<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleSalesInvoice;
use App\Models\VehicleInventory;
use App\Models\Customer;
use App\Models\VehicleMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleSalesInvoiceController extends Controller
{
    public function index()
    {
        $invoices = VehicleSalesInvoice::with('customer', 'vehicleInventory')
            ->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);
            
        return view('admin.vehicle_sales_invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('first_name')->get();
        
        $vehicles = VehicleInventory::where('status', 'available')
            ->where('is_active', true)
            ->get()
            ->map(function ($item) {
                // Find matching vehicle master
                $master = VehicleMaster::where('is_active', true)
                    ->get()
                    ->first(function ($m) use ($item) {
                        $desc = trim($m->variant_name . ' ' . $m->color_name);
                        return strtolower($desc) === strtolower($item->vehicle_description)
                            || strtolower($m->variant_name) === strtolower($item->vehicle_description);
                    });
                
                $item->ex_showroom_price = $master ? $master->ex_showroom_price : $item->purchase_price;
                $item->battery_type = $master ? $master->battery_type : 'LITHIUM';
                $item->battery_make = $master ? $master->battery_make : 'LITHIUM';
                return $item;
            });

        return view('admin.vehicle_sales_invoices.create', compact('customers', 'vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_age' => 'nullable|integer|min:0',
            'customer_occupation' => 'nullable|string|max:255',
            'customer_mobile' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'customer_residence_phone' => 'nullable|string|max:20',
            'vehicle_inventory_id' => 'required|exists:vehicle_inventories,id',
            'rate' => 'required|numeric|min:0',
            'nemmp_incentive' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:255',
            'warranty_notes' => 'nullable|string',
        ]);

        $vehicle = VehicleInventory::findOrFail($request->vehicle_inventory_id);
        if ($vehicle->status !== 'available') {
            return back()->withErrors(['vehicle_inventory_id' => 'This vehicle is not available.'])->withInput();
        }

        // Calculations
        $rate = floatval($request->rate);
        $cgst_rate = 2.50;
        $sgst_rate = 2.50;
        
        $sub_total = $rate;
        $cgst_amount = round(($sub_total * $cgst_rate) / 100, 2);
        $sgst_amount = round(($sub_total * $sgst_rate) / 100, 2);
        
        $total = $sub_total + $cgst_amount + $sgst_amount;
        
        $nemmp = floatval($request->input('nemmp_incentive', 0));
        $discount = floatval($request->input('discount', 0));
        
        $grand_total = $total - $nemmp - $discount;

        $invoice = DB::transaction(function () use ($request, $vehicle, $sub_total, $cgst_rate, $cgst_amount, $sgst_rate, $sgst_amount, $total, $nemmp, $discount, $grand_total) {
            // Generate invoice number
            $last = DB::table('vehicle_sales_invoices')->lockForUpdate()->orderBy('id', 'desc')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Mark vehicle as sold
            $vehicle->update(['status' => 'sold']);

            return VehicleSalesInvoice::create([
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $request->invoice_date,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_age' => $request->customer_age,
                'customer_occupation' => $request->customer_occupation,
                'customer_mobile' => $request->customer_mobile,
                'customer_address' => $request->customer_address,
                'customer_residence_phone' => $request->customer_residence_phone,
                'vehicle_inventory_id' => $vehicle->id,
                'rate' => $rate,
                'sub_total' => $sub_total,
                'cgst_rate' => $cgst_rate,
                'cgst_amount' => $cgst_amount,
                'sgst_rate' => $sgst_rate,
                'sgst_amount' => $sgst_amount,
                'total' => $total,
                'nemmp_incentive' => $nemmp,
                'discount' => $discount,
                'grand_total' => $grand_total,
                'payment_mode' => $request->payment_mode,
                'warranty_notes' => $request->input('warranty_notes', "MOTOR, CONTROLLER WARRANTY - 1 YEAR\nBATTERY WARRANTY - 3 YEAR\nCHARGER WARRANTY - 2 YEAR"),
            ]);
        });

        return redirect()->route('admin.vehicle-sales-invoices.show', $invoice)->withSuccess('Vehicle Sales Invoice created successfully.');
    }

    public function show(VehicleSalesInvoice $vehicleSalesInvoice)
    {
        $vehicleSalesInvoice->load('customer', 'vehicleInventory.purchaseOrder');
        
        // Find matching vehicle master for battery info
        $vehicle = $vehicleSalesInvoice->vehicleInventory;
        $master = VehicleMaster::where('is_active', true)
            ->get()
            ->first(function ($m) use ($vehicle) {
                $desc = trim($m->variant_name . ' ' . $m->color_name);
                return strtolower($desc) === strtolower($vehicle->vehicle_description)
                    || strtolower($m->variant_name) === strtolower($vehicle->vehicle_description);
            });
            
        $battery_type = $master ? $master->battery_type : 'LITHIUM';
        $battery_make = $master ? $master->battery_make : 'LITHIUM';
        $color_name = $master ? $master->color_name : '-';

        return view('admin.vehicle_sales_invoices.show', compact('vehicleSalesInvoice', 'battery_type', 'battery_make', 'color_name'));
    }

    public function destroy(VehicleSalesInvoice $vehicleSalesInvoice)
    {
        DB::transaction(function () use ($vehicleSalesInvoice) {
            $vehicle = VehicleInventory::find($vehicleSalesInvoice->vehicle_inventory_id);
            if ($vehicle) {
                $vehicle->update(['status' => 'available']);
            }
            $vehicleSalesInvoice->delete();
        });

        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully.']);
    }
}
