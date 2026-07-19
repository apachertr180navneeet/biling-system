<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\SparePartStock;
use App\Models\VehicleInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function ledger(Request $request)
    {
        $customers = Customer::orderBy('first_name')->get();
        $selectedCustomer = null;
        $transactions = collect();

        if ($request->customer_id) {
            $selectedCustomer = Customer::findOrFail($request->customer_id);
            $invoices = Invoice::where('customer_id', $request->customer_id)
                ->where('status', 'confirmed')
                ->orderBy('invoice_date')->get();
            $payments = Payment::where('customer_id', $request->customer_id)
                ->orderBy('payment_date')->get();

            $transactions = collect();

            foreach ($invoices as $inv) {
                $transactions->push([
                    'date' => $inv->invoice_date,
                    'type' => 'Invoice',
                    'no' => $inv->invoice_number,
                    'debit' => $inv->grand_total,
                    'credit' => 0,
                    'balance' => 0,
                ]);
            }

            foreach ($payments as $pay) {
                $transactions->push([
                    'date' => $pay->payment_date,
                    'type' => 'Payment',
                    'no' => $pay->payment_number,
                    'debit' => 0,
                    'credit' => $pay->amount,
                    'balance' => 0,
                ]);
            }

            $transactions = $transactions->sortBy('date')->values();
            $balance = 0;
            foreach ($transactions as &$t) {
                $balance += $t['debit'] - $t['credit'];
                $t['balance'] = $balance;
            }
        }

        return view('admin.reports.ledger', compact('customers', 'selectedCustomer', 'transactions'));
    }

    public function purchaseParts()
    {
        $stocks = SparePartStock::with('sparePart', 'purchaseOrder')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalValue = $stocks->sum(fn($s) => $s->quantity * $s->purchase_price);
        return view('admin.reports.purchase_parts', compact('stocks', 'totalValue'));
    }

    public function vehicleStock()
    {
        $vehicles = VehicleInventory::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        $totalQty = $vehicles->sum('quantity');
        $totalValue = $vehicles->sum(fn($v) => $v->quantity * $v->purchase_price);
        return view('admin.reports.vehicle_stock', compact('vehicles', 'totalQty', 'totalValue'));
    }

    public function gstr1()
    {
        $months = [];
        for ($m = 4; $m <= 12; $m++) {
            $months[] = ['value' => $m, 'label' => date('F', mktime(0, 0, 0, $m, 1))];
        }
        for ($m = 1; $m <= 3; $m++) {
            $months[] = ['value' => $m, 'label' => date('F', mktime(0, 0, 0, $m, 1))];
        }
        $years = range(date('Y') - 1, date('Y') + 1);

        return view('admin.reports.gstr1', compact('months', 'years'));
    }

    public function gstr1Export(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        $month = $request->month;
        $year = $request->year;

        $fyStart = $month >= 4 ? $year : $year - 1;
        $fyEnd = $fyStart + 1;
        $fiscalYear = $fyStart . '-' . substr($fyEnd, -2);

        $invoices = Invoice::with('customer', 'items')
            ->where('is_gst', true)
            ->where('status', 'confirmed')
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->orderBy('invoice_date')
            ->get();

        $csv = "GSTR-1 Export - {$fiscalYear}\n";
        $csv .= "Month: " . date('F', mktime(0, 0, 0, $month, 1)) . " {$year}\n\n";
        $csv .= "Invoice No,Date,Customer Name,GSTIN,Taxable Value,GST Rate,IGST,CGST,SGST,Cess,Total\n";

        foreach ($invoices as $inv) {
            $customer = $inv->customer;
            $gstRate = $inv->invoice_type === 'vehicle' ? 28 : ($inv->items->first()->gst_rate ?? 0);
            $igst = $inv->igst_amount ?? ($inv->gst_type === 'igst' ? $inv->gst_amount : 0);
            $cgst = $inv->cgst_amount ?? ($inv->gst_type === 'cgst_sgst' ? round($inv->gst_amount / 2, 2) : 0);
            $sgst = $inv->sgst_amount ?? ($inv->gst_type === 'cgst_sgst' ? round($inv->gst_amount - $cgst, 2) : 0);

            $csv .= implode(',', [
                $inv->invoice_number,
                $inv->invoice_date->format('d-m-Y'),
                ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''),
                $customer->gstin ?? '',
                number_format($inv->subtotal, 2),
                $gstRate . '%',
                number_format($igst, 2),
                number_format($cgst, 2),
                number_format($sgst, 2),
                number_format($inv->cess_amount, 2),
                number_format($inv->grand_total, 2),
            ]) . "\n";
        }

        $filename = "GSTR1_{$fiscalYear}_M" . str_pad($month, 2, '0', STR_PAD_LEFT) . ".csv";
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
