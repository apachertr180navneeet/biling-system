<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceAmc;
use App\Models\MaintenanceAsset;
use App\Models\Vendor;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class AmcController extends Controller
{
    public function index()
    {
        return view('admin.maintenance.amcs.index');
    }

    public function data(Request $request)
    {
        $query = MaintenanceAmc::with(['asset', 'vendor'])->select('maintenance_amcs.*');
        return DataTables::of($query)
            ->filterColumn('contract_number', function ($query, $value) {
                $query->where('contract_number', 'like', "%{$value}%");
            })
            ->addColumn('asset_name', fn($a) => $a->asset?->name ?? '-')
            ->addColumn('vendor_name', fn($a) => $a->vendor?->company_name ?? '-')
            ->addColumn('edit_url', fn($a) => route('admin.maintenance.amcs.edit', $a))
            ->addColumn('delete_url', fn($a) => route('admin.maintenance.amcs.destroy', $a))
            ->addColumn('show_url', fn($a) => route('admin.maintenance.amcs.show', $a))
            ->addColumn('status_url', fn($a) => route('admin.maintenance.amcs.status', $a))
            ->rawColumns([])
            ->make(true);
    }

    public function show(MaintenanceAmc $amc)
    {
        $amc->load('asset', 'vendor');
        return view('admin.maintenance.amcs.show', ['amc' => $amc]);
    }

    public function create()
    {
        $amc = null;
        $assets = MaintenanceAsset::orderBy('name')->get();
        $vendors = Vendor::where('status', 'active')->orderBy('company_name')->get();
        return view('admin.maintenance.amcs.form', compact('amc', 'assets', 'vendors'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'asset_id' => 'required|exists:maintenance_assets,id',
                'vendor_id' => 'nullable|exists:vendors,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'cost' => 'required|numeric|min:0',
                'contact_person' => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:30',
                'status' => 'required|in:active,expired,cancelled',
                'description' => 'nullable|string',
            ]);

            MaintenanceAmc::create([
                'hotel_id' => auth()->user()->branch_id ?? Hotel::first()?->id,
                'asset_id' => $request->asset_id,
                'vendor_id' => $request->vendor_id,
                'contract_number' => Helper::slug('maintenance_amcs', 'AMC-' . now()->format('YmdHis'), 'contract_number'),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'cost' => $request->cost,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'status' => $request->status,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.maintenance.amcs.index')->with('success', 'AMC created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(MaintenanceAmc $amc)
    {
        $assets = MaintenanceAsset::orderBy('name')->get();
        $vendors = Vendor::where('status', 'active')->orderBy('company_name')->get();
        return view('admin.maintenance.amcs.form', compact('amc', 'assets', 'vendors'));
    }

    public function update(Request $request, MaintenanceAmc $amc)
    {
        try {
            $request->validate([
                'asset_id' => 'required|exists:maintenance_assets,id',
                'vendor_id' => 'nullable|exists:vendors,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'cost' => 'required|numeric|min:0',
                'contact_person' => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:30',
                'status' => 'required|in:active,expired,cancelled',
                'description' => 'nullable|string',
            ]);

            $amc->update([
                'asset_id' => $request->asset_id,
                'vendor_id' => $request->vendor_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'cost' => $request->cost,
                'contact_person' => $request->contact_person,
                'contact_phone' => $request->contact_phone,
                'status' => $request->status,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.maintenance.amcs.index')->with('success', 'AMC updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(MaintenanceAmc $amc)
    {
        try {
            $amc->delete();
            return response()->json(['success' => true, 'message' => 'AMC deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function status(MaintenanceAmc $amc)
    {
        $statusFlow = [
            'active' => 'expired',
            'expired' => 'cancelled',
        ];
        $next = $statusFlow[$amc->status] ?? null;
        if ($next) {
            $amc->update(['status' => $next]);
            return response()->json(['success' => true, 'message' => 'Status updated to ' . $next]);
        }
        return response()->json(['success' => false, 'message' => 'Cannot change status.'], 422);
    }
}
