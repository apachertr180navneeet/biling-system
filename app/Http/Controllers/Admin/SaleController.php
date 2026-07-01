<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('admin.sales.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_description' => 'required|string|max:255',
            'sale_price' => 'required|numeric|min:0',
            'booking_date' => 'required|date',
            'booking_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $last = Sale::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $data['sale_number'] = 'SL-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $data['status'] = 'booking';

        Sale::create($data);
        return redirect()->route('admin.sales.index')->withSuccess('Sale created successfully.');
    }

    public function edit(Sale $sale)
    {
        $customers = Customer::orderBy('first_name')->get();
        return view('admin.sales.edit', compact('sale', 'customers'));
    }

    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_description' => 'required|string|max:255',
            'sale_price' => 'required|numeric|min:0',
            'booking_date' => 'required|date',
            'booking_amount' => 'required|numeric|min:0',
            'allotment_date' => 'nullable|date',
            'registration_date' => 'nullable|date',
            'reg_number' => 'nullable|string|max:50',
            'delivery_date' => 'nullable|date',
            'status' => 'required|in:booking,allotment,registration,delivery,completed',
            'notes' => 'nullable|string',
        ]);

        $sale->update($data);

        return redirect()->route('admin.sales.index')->withSuccess('Sale updated successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load('customer');
        return view('admin.sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(Sale $sale)
    {
        $sale->update(['is_active' => !$sale->is_active]);
        return response()->json(['success' => true, 'is_active' => $sale->is_active]);
    }

    public function updateStatus(Sale $sale, Request $request)
    {
        $request->validate(['status' => 'required|in:booking,allotment,registration,delivery,completed']);

        $data = ['status' => $request->status];

        switch ($request->status) {
            case 'allotment':
                $data['allotment_date'] = now();
                break;
            case 'registration':
                $data['registration_date'] = now();
                break;
            case 'delivery':
            case 'completed':
                $data['delivery_date'] = now();
                break;
        }

        $sale->update($data);
        return response()->json(['success' => true, 'status' => $sale->status]);
    }

    public function generateInvoice(Sale $sale)
    {
        $sale->load('customer');
        return redirect()->route('admin.invoices.create-vehicle', [
            'customer_id' => $sale->customer_id,
            'vehicle_description' => $sale->vehicle_description,
            'sale_id' => $sale->id,
            'sale_price' => $sale->sale_price,
        ]);
    }
}
