<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceAsset;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class AssetController extends Controller
{
    public function index()
    {
        return view('admin.maintenance.assets.index');
    }

    public function data(Request $request)
    {
        $query = MaintenanceAsset::select('maintenance_assets.*');
        return DataTables::of($query)
            ->filterColumn('asset_code', function ($query, $value) {
                $query->where('asset_code', 'like', "%{$value}%");
            })
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('edit_url', fn($a) => route('admin.maintenance.assets.edit', $a))
            ->addColumn('delete_url', fn($a) => route('admin.maintenance.assets.destroy', $a))
            ->addColumn('show_url', fn($a) => route('admin.maintenance.assets.show', $a))
            ->rawColumns([])
            ->make(true);
    }

    public function show(MaintenanceAsset $asset)
    {
        $asset->load('amcs.vendor', 'workOrders.assignedEmployee');
        return view('admin.maintenance.assets.show', ['asset' => $asset]);
    }

    public function create()
    {
        $asset = null;
        return view('admin.maintenance.assets.form', compact('asset'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'serial_number' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'purchase_cost' => 'nullable|numeric|min:0',
                'warranty_expiry_date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive,under_maintenance,disposed',
                'description' => 'nullable|string',
            ]);

            MaintenanceAsset::create([
                'hotel_id' => auth()->user()->branch_id ?? Hotel::first()?->id,
                'asset_code' => Helper::slug('maintenance_assets', 'AST-' . now()->format('YmdHis'), 'asset_code'),
                'name' => $request->name,
                'category' => $request->category,
                'brand' => $request->brand,
                'model' => $request->model,
                'serial_number' => $request->serial_number,
                'purchase_date' => $request->purchase_date,
                'purchase_cost' => $request->purchase_cost,
                'warranty_expiry_date' => $request->warranty_expiry_date,
                'location' => $request->location,
                'status' => $request->status,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.maintenance.assets.index')->with('success', 'Asset created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(MaintenanceAsset $asset)
    {
        return view('admin.maintenance.assets.form', compact('asset'));
    }

    public function update(Request $request, MaintenanceAsset $asset)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'serial_number' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'purchase_cost' => 'nullable|numeric|min:0',
                'warranty_expiry_date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive,under_maintenance,disposed',
                'description' => 'nullable|string',
            ]);

            $asset->update([
                'name' => $request->name,
                'category' => $request->category,
                'brand' => $request->brand,
                'model' => $request->model,
                'serial_number' => $request->serial_number,
                'purchase_date' => $request->purchase_date,
                'purchase_cost' => $request->purchase_cost,
                'warranty_expiry_date' => $request->warranty_expiry_date,
                'location' => $request->location,
                'status' => $request->status,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.maintenance.assets.index')->with('success', 'Asset updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(MaintenanceAsset $asset)
    {
        try {
            $asset->delete();
            return response()->json(['success' => true, 'message' => 'Asset deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
