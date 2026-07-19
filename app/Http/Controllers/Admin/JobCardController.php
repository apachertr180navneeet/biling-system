<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\Customer;
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
        $services = Service::with('category')->orderBy('name')->get();
        $spareParts = SparePart::with('category')->orderBy('name')->get();
        return view('admin.job_cards.create', compact('customers', 'services', 'spareParts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_number' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:255',
            'kilometer_reading' => 'nullable|string|max:50',
            'complaint' => 'nullable|string',
            'service_date' => 'required|date',
            'notes' => 'nullable|string',
            'is_gst' => 'nullable|boolean',
        ]);

        $data['status'] = 'pending';
        $data['is_gst'] = $request->boolean('is_gst');

        $data['total_labor'] = 0;
        $data['total_parts'] = 0;
        $data['subtotal'] = 0;
        $data['gst_amount'] = 0;
        $data['cess_amount'] = 0;
        $data['grand_total'] = 0;

        $jobCard = DB::transaction(function () use ($data) {
            $last = DB::table('job_cards')->lockForUpdate()->orderBy('id', 'desc')->first();
            $nextId = $last ? $last->id + 1 : 1;
            $data['job_card_number'] = 'JC-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            return JobCard::create($data);
        });

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
        $services = Service::with('category')->orderBy('name')->get();
        $spareParts = SparePart::with('category')->orderBy('name')->get();
        $jobCard->load('services', 'parts');
        return view('admin.job_cards.edit', compact('jobCard', 'customers', 'services', 'spareParts'));
    }

    public function update(Request $request, JobCard $jobCard)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
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
        $jobCard->load('customer', 'services.service', 'parts.sparePart');
        return view('admin.job_cards.show', compact('jobCard'));
    }

    public function destroy(JobCard $jobCard)
    {
        DB::transaction(function () use ($jobCard) {
            $jobCard->load('parts');
            foreach ($jobCard->parts as $part) {
                if ($part->spare_part_id) {
                    $stock = SparePartStock::where('spare_part_id', $part->spare_part_id)->lockForUpdate()->first();
                    if ($stock) {
                        $stock->increment('quantity', $part->quantity);
                    }
                }
            }
            $jobCard->services()->delete();
            $jobCard->parts()->delete();
            $jobCard->delete();
        });
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function toggleStatus(JobCard $jobCard)
    {
        $jobCard->update(['is_active' => !$jobCard->is_active]);
        $jobCard->refresh();
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

        $gstCalc = new GstCalculator();
        $gstItems = [];
        foreach ($request->items as $item) {
            $gstItems[] = [
                'description' => $item['name'],
                'quantity' => ($item['type'] === 'part' ? ($item['qty'] ?? 1) : 1),
                'unit_price' => $item['rate'],
                'gst_rate' => $item['gst_rate'] ?? 0,
                'cess_rate' => 0,
                'spare_part_id' => $item['spare_part_id'] ?? null,
            ];
        }
        $result = $gstCalc->calculateForItems($gstItems, $jobCard->is_gst, $jobCard->customer);

        DB::transaction(function () use ($jobCard, $request, $result) {
            $jobCard->load('parts');
            foreach ($jobCard->parts as $oldPart) {
                if ($oldPart->spare_part_id) {
                    $stock = SparePartStock::where('spare_part_id', $oldPart->spare_part_id)->lockForUpdate()->first();
                    if ($stock) {
                        $stock->increment('quantity', $oldPart->quantity);
                    }
                }
            }

            $jobCard->services()->delete();
            $jobCard->parts()->delete();

            $totalLabor = 0;
            $totalPartsTotal = 0;

            foreach ($request->items as $i => $item) {
                $ci = $result['calculatedItems'][$i] ?? null;
                if (!$ci) continue;

                if ($item['type'] === 'service') {
                    $totalLabor += $item['rate'];
                    $jobCard->services()->create([
                        'service_name' => $item['name'],
                        'labor_charge' => $item['rate'],
                    ]);
                } else {
                    $totalPartsTotal += $ci['total'];
                    $jobCard->parts()->create([
                        'spare_part_id' => $item['spare_part_id'] ?? null,
                        'part_name' => $item['name'],
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
                        if ($stock && $stock->quantity >= $ci['quantity']) {
                            $stock->decrement('quantity', $ci['quantity']);
                        }
                    }
                }
            }

            $jobCard->update([
                'total_labor' => $totalLabor,
                'total_parts' => $totalPartsTotal,
                'subtotal' => $result['subtotal'],
                'gst_type' => $result['gstType'],
                'gst_amount' => $result['totalGst'],
                'cess_amount' => $result['totalCess'],
                'grand_total' => $result['grandTotal'],
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
