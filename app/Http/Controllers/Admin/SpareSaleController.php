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
        $spareParts = SparePart::orderBy('name')->get();
        return view('admin.spare_sales.create', compact('customers', 'spareParts'));
    }

    public function store(Request $request, GstCalculator $gst)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'payment_mode' => 'required|in:cash,bank_transfer,cheque,upi,card',
            'is_gst' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'nullable|exists:spare_parts,id',
            'items.*.part_name' => 'required|string',
            'items.*.hsn_code' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'nullable|numeric|min:0',
        ]);

        $data['is_gst'] = $request->boolean('is_gst', true);

        $customer = !empty($data['customer_id']) ? Customer::find($data['customer_id']) : null;

        $gstItems = [];
        foreach ($request->items as $item) {
            $gstItems[] = [
                'description' => $item['part_name'],
                'spare_part_id' => $item['spare_part_id'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['rate'],
                'gst_rate' => $item['gst_rate'] ?? 0,
                'cess_rate' => 0,
            ];
        }

        $result = $gst->calculateForItems($gstItems, $data['is_gst'], $customer);

        DB::transaction(function () use ($data, $request, $result) {
            $last = DB::table('spare_sales')->lockForUpdate()->orderBy('id', 'desc')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $data['sale_number'] = 'SS-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $sale = SpareSale::create([
                'sale_number' => $data['sale_number'],
                'customer_id' => $data['customer_id'],
                'sale_date' => $data['sale_date'],
                'is_gst' => $data['is_gst'],
                'gst_type' => $result['gstType'],
                'payment_mode' => $data['payment_mode'],
                'notes' => $data['notes'],
                'subtotal' => $result['subtotal'],
                'gst_amount' => $result['totalGst'],
                'grand_total' => $result['grandTotal'],
            ]);

            foreach ($result['calculatedItems'] as $i => $ci) {
                $item = $request->items[$i];

                $sale->items()->create([
                    'spare_part_id' => $item['spare_part_id'] ?? null,
                    'part_name' => $item['part_name'],
                    'hsn_code' => $item['hsn_code'] ?? null,
                    'quantity' => $ci['quantity'],
                    'rate' => $item['rate'],
                    'gst_rate' => $ci['gst_rate'],
                    'gst_amount' => $ci['gst_amount'],
                    'cgst_amount' => $ci['cgst_amount'],
                    'sgst_amount' => $ci['sgst_amount'],
                    'igst_amount' => $ci['igst_amount'],
                    'total' => $ci['total'],
                ]);

                if (!empty($item['spare_part_id'])) {
                    $stock = SparePartStock::where('spare_part_id', $item['spare_part_id'])->lockForUpdate()->first();
                    if (!$stock || $stock->quantity < $ci['quantity']) {
                        throw new \Exception("Insufficient stock for spare part ID {$item['spare_part_id']}. Available: " . ($stock->quantity ?? 0) . ", requested: {$ci['quantity']}.");
                    }
                    $stock->decrement('quantity', $ci['quantity']);
                }
            }
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
