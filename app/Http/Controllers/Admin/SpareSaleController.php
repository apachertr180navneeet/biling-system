<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpareSale;
use App\Models\SpareSaleItem;
use App\Models\SparePart;
use App\Models\SparePartStock;
use App\Models\Customer;
use App\Services\GstCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpareSaleController extends Controller
{
    public function index()
    {
        $sales = SpareSale::with('customer')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.spare_sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        $spareParts = SparePart::with('category')->orderBy('name')->get();
        return view('admin.spare_sales.create', compact('customers', 'spareParts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'payment_mode' => 'required|in:cash,bank_transfer,cheque,upi,card',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'nullable|exists:spare_parts,id',
            'items.*.part_name' => 'required|string',
            'items.*.hsn_code' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'nullable|numeric|min:0',
        ]);

        $last = SpareSale::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $data['sale_number'] = 'SS-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($data, $request) {
            $subtotal = 0;
            $totalGst = 0;

            $sale = SpareSale::create([
                'sale_number' => $data['sale_number'],
                'customer_id' => $data['customer_id'],
                'sale_date' => $data['sale_date'],
                'payment_mode' => $data['payment_mode'],
                'notes' => $data['notes'],
                'subtotal' => 0,
                'gst_amount' => 0,
                'grand_total' => 0,
            ]);

            foreach ($request->items as $item) {
                $gstRate = $item['gst_rate'] ?? 0;
                $lineTotal = $item['rate'] * $item['quantity'];
                $gstAmount = ($lineTotal * $gstRate) / 100;
                $total = $lineTotal + $gstAmount;
                $subtotal += $lineTotal;
                $totalGst += $gstAmount;

                $sale->items()->create([
                    'spare_part_id' => $item['spare_part_id'],
                    'part_name' => $item['part_name'],
                    'hsn_code' => $item['hsn_code'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'gst_rate' => $gstRate,
                    'gst_amount' => $gstAmount,
                    'total' => $total,
                ]);

                if (!empty($item['spare_part_id'])) {
                    $stock = SparePartStock::where('spare_part_id', $item['spare_part_id'])->first();
                    if ($stock && $stock->quantity >= $item['quantity']) {
                        $stock->decrement('quantity', $item['quantity']);
                    }
                }

            }

            $sale->update([
                'subtotal' => $subtotal,
                'gst_amount' => $totalGst,
                'grand_total' => $subtotal + $totalGst,
            ]);
        });

        return redirect()->route('admin.spare-sales.index')->withSuccess('Spare sale completed successfully.');
    }

    public function show(SpareSale $spareSale)
    {
        $spareSale->load('customer', 'items.sparePart');
        return view('admin.spare_sales.show', compact('spareSale'));
    }

    public function print(SpareSale $spareSale)
    {
        $spareSale->load('customer', 'items');
        return view('admin.spare_sales.print', compact('spareSale'));
    }

    public function destroy(SpareSale $spareSale)
    {
        DB::transaction(function () use ($spareSale) {
            $spareSale->load('items');
            foreach ($spareSale->items as $item) {
                if ($item->spare_part_id) {
                    $stock = SparePartStock::firstOrCreate(
                        ['spare_part_id' => $item->spare_part_id],
                        ['quantity' => 0, 'min_quantity' => 0, 'purchase_price' => 0]
                    );
                    $stock->increment('quantity', $item->quantity);
                }
            }
            $spareSale->items()->delete();
            $spareSale->delete();
        });
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
}
