<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartSalesInvoice;
use App\Models\PartSalesInvoiceItem;
use App\Models\Customer;
use App\Models\SparePart;
use App\Models\SparePartStock;
use App\Models\SparePartStockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartSalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = PartSalesInvoice::with('customer', 'items')
            ->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_mobile', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(20);

        return view('admin.part_sales_invoices.index', compact('invoices', 'search'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('first_name')->get();
        
        $spareParts = SparePart::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($part) {
                $stock = SparePartStock::where('spare_part_id', $part->id)->first();
                $part->qty_available = $stock ? $stock->quantity : 0;
                return $part;
            });

        return view('admin.part_sales_invoices.create', compact('customers', 'spareParts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'customer_gstin' => 'nullable|string|max:15',
            'customer_pan' => 'nullable|string|max:10',
            'place_of_supply' => 'required|string|max:255',
            'payment_mode' => 'required|string|max:255',
            'previous_balance' => 'nullable|numeric|min:0',
            'received_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'required|exists:spare_parts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.tax_percentage' => 'required|numeric|min:0|max:100',
            'items.*.serial_no_warranty_notes' => 'nullable|string|max:255',
        ]);

        // Validate stock availability first
        foreach ($request->items as $itemData) {
            $part = SparePart::findOrFail($itemData['spare_part_id']);
            $stock = SparePartStock::where('spare_part_id', $part->id)->first();
            $available = $stock ? $stock->quantity : 0;
            if ($available < intval($itemData['quantity'])) {
                return back()->withErrors([
                    'items' => "Insufficient stock for part: {$part->name}. Available: {$available}, Requested: {$itemData['quantity']}"
                ])->withInput();
            }
        }

        // Calculations
        $taxable_amount = 0;
        $cgst_amount = 0;
        $sgst_amount = 0;
        $subtotal = 0;

        foreach ($request->items as $itemData) {
            $qty = intval($itemData['quantity']);
            $rate = floatval($itemData['rate']);
            $tax_pct = floatval($itemData['tax_percentage']);

            $line_taxable = $qty * $rate;
            $line_tax = ($line_taxable * $tax_pct) / 100;
            
            $taxable_amount += $line_taxable;
            $cgst_amount += round($line_tax / 2, 2);
            $sgst_amount += round($line_tax / 2, 2);
            $subtotal += ($line_taxable + $line_tax);
        }

        $prev_bal = floatval($request->input('previous_balance', 0));
        $received = floatval($request->input('received_amount', 0));
        
        $total_before_round = $subtotal;
        $total_rounded = round($total_before_round);
        $round_off = $total_rounded - $total_before_round;

        $grand_total = $total_rounded;
        $balance = $grand_total - $received;
        $curr_bal = $prev_bal + $balance;

        $invoice = DB::transaction(function () use ($request, $taxable_amount, $cgst_amount, $sgst_amount, $round_off, $total_rounded, $received, $balance, $prev_bal, $curr_bal) {
            // Generate Invoice number
            $last = DB::table('part_sales_invoices')->lockForUpdate()->orderBy('id', 'desc')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $invoiceNumber = 'INV-P-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $inv = PartSalesInvoice::create([
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $request->invoice_date,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_mobile' => $request->customer_mobile,
                'customer_address' => $request->customer_address,
                'customer_gstin' => $request->customer_gstin,
                'customer_pan' => $request->customer_pan,
                'place_of_supply' => $request->place_of_supply,
                'taxable_amount' => $taxable_amount,
                'cgst_amount' => $cgst_amount,
                'sgst_amount' => $sgst_amount,
                'round_off' => $round_off,
                'total_amount' => $total_rounded,
                'received_amount' => $received,
                'balance' => $balance,
                'payment_mode' => $request->payment_mode,
                'previous_balance' => $prev_bal,
                'current_balance' => $curr_bal,
                'is_active' => true,
            ]);

            foreach ($request->items as $itemData) {
                $qty = intval($itemData['quantity']);
                $rate = floatval($itemData['rate']);
                $tax_pct = floatval($itemData['tax_percentage']);

                $line_taxable = $qty * $rate;
                $line_tax = ($line_taxable * $tax_pct) / 100;
                $line_amount = $line_taxable + $line_tax;

                // Decrement stock
                $stock = SparePartStock::where('spare_part_id', $itemData['spare_part_id'])->lockForUpdate()->first();
                $stock->decrement('quantity', $qty);

                // Transaction log
                SparePartStockTransaction::create([
                    'spare_part_id' => $itemData['spare_part_id'],
                    'transaction_type' => 'out',
                    'quantity' => $qty,
                    'reference_no' => $invoiceNumber,
                    'notes' => 'Sold via Parts Sales Invoice #' . $invoiceNumber,
                ]);

                PartSalesInvoiceItem::create([
                    'part_sales_invoice_id' => $inv->id,
                    'spare_part_id' => $itemData['spare_part_id'],
                    'quantity' => $qty,
                    'rate' => $rate,
                    'tax_percentage' => $tax_pct,
                    'tax_amount' => $line_tax,
                    'amount' => $line_amount,
                    'serial_no_warranty_notes' => $itemData['serial_no_warranty_notes'],
                ]);
            }

            return $inv;
        });

        return redirect()->route('admin.part-sales-invoices.show', $invoice)->withSuccess('Parts Sales Invoice created successfully.');
    }

    public function show(PartSalesInvoice $partSalesInvoice)
    {
        $partSalesInvoice->load('customer', 'items.sparePart');
        return view('admin.part_sales_invoices.show', compact('partSalesInvoice'));
    }

    public function destroy(PartSalesInvoice $partSalesInvoice)
    {
        DB::transaction(function () use ($partSalesInvoice) {
            $partSalesInvoice->load('items');
            foreach ($partSalesInvoice->items as $item) {
                $stock = SparePartStock::firstOrCreate(
                    ['spare_part_id' => $item->spare_part_id],
                    ['quantity' => 0, 'min_quantity' => 0, 'purchase_price' => 0]
                );
                $stock->increment('quantity', $item->quantity);

                // Transaction log for restore
                SparePartStockTransaction::create([
                    'spare_part_id' => $item->spare_part_id,
                    'transaction_type' => 'in',
                    'quantity' => $item->quantity,
                    'reference_no' => $partSalesInvoice->invoice_number,
                    'notes' => 'Restored via Parts Sales Invoice deletion #' . $partSalesInvoice->invoice_number,
                ]);
            }

            $partSalesInvoice->items()->delete();
            $partSalesInvoice->delete();
        });

        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully.']);
    }
}
