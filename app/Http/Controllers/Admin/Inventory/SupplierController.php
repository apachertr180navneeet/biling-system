<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventorySupplier;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class SupplierController extends Controller
{
    public function index()
    {
        return view('admin.inventory.suppliers.index');
    }

    public function data(Request $request)
    {
        $query = InventorySupplier::query();
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($supplier) => route('admin.inventory.suppliers.status', $supplier))
            ->addColumn('edit_url', fn($supplier) => route('admin.inventory.suppliers.edit', $supplier))
            ->addColumn('delete_url', fn($supplier) => route('admin.inventory.suppliers.destroy', $supplier))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $supplier = null;
        return view('admin.inventory.suppliers.form', compact('supplier'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'gst_number' => 'nullable|string|max:50',
                'payment_terms' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('inventory_suppliers', $request->name);
            InventorySupplier::create($data);
            return redirect()->route('admin.inventory.suppliers.index')->with('success', 'Supplier created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(InventorySupplier $supplier)
    {
        return view('admin.inventory.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, InventorySupplier $supplier)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'gst_number' => 'nullable|string|max:50',
                'payment_terms' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);
            $supplier->update($request->all());
            return redirect()->route('admin.inventory.suppliers.index')->with('success', 'Supplier updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, InventorySupplier $supplier)
    {
        try {
            $supplier->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Supplier deleted successfully!']);
            }
            return redirect()->route('admin.inventory.suppliers.index')->with('success', 'Supplier deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, InventorySupplier $supplier)
    {
        try {
            $supplier->status = $supplier->status === 'active' ? 'inactive' : 'active';
            $supplier->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $supplier->status, 'message' => 'Supplier status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Supplier status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
