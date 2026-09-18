<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryUnit;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class UnitController extends Controller
{
    public function index()
    {
        return view('admin.inventory.units.index');
    }

    public function data(Request $request)
    {
        $query = InventoryUnit::query();
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($unit) => route('admin.inventory.units.status', $unit))
            ->addColumn('edit_url', fn($unit) => route('admin.inventory.units.edit', $unit))
            ->addColumn('delete_url', fn($unit) => route('admin.inventory.units.destroy', $unit))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $unit = null;
        return view('admin.inventory.units.form', compact('unit'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'short_name' => 'required|string|max:50',
                'status' => 'required|in:active,inactive',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('inventory_units', $request->name);
            InventoryUnit::create($data);
            return redirect()->route('admin.inventory.units.index')->with('success', 'Unit created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(InventoryUnit $unit)
    {
        return view('admin.inventory.units.form', compact('unit'));
    }

    public function update(Request $request, InventoryUnit $unit)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'short_name' => 'required|string|max:50',
                'status' => 'required|in:active,inactive',
            ]);
            $unit->update($request->all());
            return redirect()->route('admin.inventory.units.index')->with('success', 'Unit updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, InventoryUnit $unit)
    {
        try {
            $unit->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Unit deleted successfully!']);
            }
            return redirect()->route('admin.inventory.units.index')->with('success', 'Unit deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, InventoryUnit $unit)
    {
        try {
            $unit->status = $unit->status === 'active' ? 'inactive' : 'active';
            $unit->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $unit->status, 'message' => 'Unit status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Unit status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
