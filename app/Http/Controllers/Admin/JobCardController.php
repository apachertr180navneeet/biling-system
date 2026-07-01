<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\Customer;
use App\Models\VehicleStock;
use App\Models\Service;
use App\Models\SparePart;
use App\Models\SparePartStock;
use App\Services\GstCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobCardController extends Controller
{
    public function index()
    {
        $jobCards = JobCard::with('customer')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.job_cards.index', compact('jobCards'));
    }

    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        $vehicleStocks = VehicleStock::where('status', 'available')->get();
        $services = Service::with('category')->orderBy('name')->get();
        $spareParts = SparePart::with('category')->orderBy('part_name')->get();
        return view('admin.job_cards.create', compact('customers', 'vehicleStocks', 'services', 'spareParts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_stock_id' => 'nullable|exists:vehicle_stocks,id',
            'vehicle_number' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:255',
            'kilometer_reading' => 'nullable|string|max:50',
            'complaint' => 'nullable|string',
            'service_date' => 'required|date',
            'notes' => 'nullable|string',
            'is_gst' => 'nullable|boolean',
        ]);

        $last = JobCard::orderBy('id', 'desc')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $data['job_card_number'] = 'JC-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $data['status'] = 'pending';
        $data['is_gst'] = $request->boolean('is_gst');

        if ($request->vehicle_stock_id) {
            $stock = VehicleStock::find($request->vehicle_stock_id);
            $data['vehicle_number'] = $stock->chassis_number;
        }

        $data['total_labor'] = 0;
        $data['total_parts'] = 0;
        $data['subtotal'] = 0;
        $data['gst_amount'] = 0;
        $data['cess_amount'] = 0;
        $data['grand_total'] = 0;

        $jobCard = JobCard::create($data);

        if ($request->filled('service_ids')) {
            foreach ($request->service_ids as $i => $serviceId) {
                if (!empty($request->labor_charges[$i])) {
                    $service = Service::find($serviceId);
                    $jobCard->services()->create([
                        'service_id' => $serviceId,
                        'service_name' => $service ? $service->name : ($request->service_names[$i] ?? ''),
                        'labor_charge' => $request->labor_charges[$i],
                    ]);
                }
            }
        }

        $jobCard->save();

        return redirect()->route('admin.job-cards.edit', $jobCard)->withSuccess('Job card created. Add parts and finalize billing.');
    }

    public function edit(JobCard $jobCard)
    {
        $customers = Customer::orderBy('first_name')->get();
        $vehicleStocks = VehicleStock::where('status', 'available')->get();
        $services = Service::with('category')->orderBy('name')->get();
        $spareParts = SparePart::with('category')->orderBy('part_name')->get();
        $jobCard->load('services', 'parts');
        return view('admin.job_cards.edit', compact('jobCard', 'customers', 'vehicleStocks', 'services', 'spareParts'));
    }

    public function update(Request $request, JobCard $jobCard)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_stock_id' => 'nullable|exists:vehicle_stocks,id',
            'vehicle_number' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:255',
            'kilometer_reading' => 'nullable|string|max:50',
            'complaint' => 'nullable|string',
            'service_date' => 'required|date',
            'completion_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,billed',
            'notes' => 'nullable|string',
        ]);

        $jobCard->update($data);

        return redirect()->route('admin.job-cards.index')->withSuccess('Job card updated successfully.');
    }

    public function show(JobCard $jobCard)
    {
        $jobCard->load('customer', 'vehicleStock', 'services.service', 'parts.sparePart');
        return view('admin.job_cards.show', compact('jobCard'));
    }

    public function destroy(JobCard $jobCard)
    {
        $jobCard->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(JobCard $jobCard)
    {
        $jobCard->update(['is_active' => !$jobCard->is_active]);
        return response()->json(['success' => true, 'is_active' => $jobCard->is_active]);
    }

    public function updateStatus(JobCard $jobCard, Request $request)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,completed,billed']);
        $jobCard->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $jobCard->status]);
    }

    public function calculateBilling(JobCard $jobCard, Request $request)
    {
        $request->validate([
            'items' => 'nullable|array',
            'items.*.type' => 'required|in:service,part',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required_if:items.*.type,part|numeric|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'nullable|numeric|min:0',
        ]);

        $totalLabor = 0;
        $totalParts = 0;

        DB::transaction(function () use ($jobCard, $request, &$totalLabor, &$totalParts) {
            $jobCard->services()->delete();
            $jobCard->parts()->delete();

            foreach ($request->items as $item) {
                if ($item['type'] === 'service') {
                    $totalLabor += $item['rate'];
                    $jobCard->services()->create([
                        'service_name' => $item['name'],
                        'labor_charge' => $item['rate'],
                    ]);
                } else {
                    $qty = $item['qty'] ?? 1;
                    $gstRate = $item['gst_rate'] ?? 0;
                    $gstAmount = ($item['rate'] * $qty * $gstRate) / 100;
                    $total = ($item['rate'] * $qty) + $gstAmount;
                    $totalParts += $total;

                    $jobCard->parts()->create([
                        'part_name' => $item['name'],
                        'quantity' => $qty,
                        'rate' => $item['rate'],
                        'gst_rate' => $gstRate,
                        'gst_amount' => $gstAmount,
                        'total' => $total,
                    ]);
                }
            }

            $subtotal = $totalLabor + $totalParts;
            $totalGst = 0;
            $gstType = null;

            if ($jobCard->is_gst) {
                $customer = $jobCard->customer;
                $gstCalc = new GstCalculator(0, $customer);
                $result = $gstCalc->calculate($subtotal);
                $totalGst = $result['total_gst'];
                $gstType = $result['type'] === 'igst' ? 'igst' : 'cgst_sgst';
            }

            $jobCard->update([
                'total_labor' => $totalLabor,
                'total_parts' => $totalParts,
                'subtotal' => $subtotal,
                'gst_type' => $gstType,
                'gst_amount' => $totalGst,
                'grand_total' => $subtotal + $totalGst,
            ]);
        });

        $jobCard->refresh();
        return response()->json(['success' => true, 'job_card' => $jobCard]);
    }

    public function print(JobCard $jobCard)
    {
        $jobCard->load('customer', 'services', 'parts');
        return view('admin.job_cards.print', compact('jobCard'));
    }
}
