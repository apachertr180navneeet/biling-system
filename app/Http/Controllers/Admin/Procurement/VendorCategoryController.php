<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorCategory;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class VendorCategoryController extends Controller
{
    public function index()
    {
        return view('admin.procurement.vendor-categories.index');
    }

    public function data(Request $request)
    {
        $query = VendorCategory::query();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($cat) => route('admin.procurement.vendor-categories.status', $cat))
            ->addColumn('edit_url', fn($cat) => route('admin.procurement.vendor-categories.edit', $cat))
            ->addColumn('delete_url', fn($cat) => route('admin.procurement.vendor-categories.destroy', $cat))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $category = null;
        return view('admin.procurement.vendor-categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:vendor_categories,name',
                'description' => 'nullable|string',
            ]);
            VendorCategory::create([
                'name' => $request->name,
                'slug' => Helper::slug('vendor_categories', $request->name),
                'description' => $request->description,
                'status' => 'active',
            ]);
            return redirect()->route('admin.procurement.vendor-categories.index')->with('success', 'Vendor category created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(VendorCategory $category)
    {
        return view('admin.procurement.vendor-categories.form', compact('category'));
    }

    public function update(Request $request, VendorCategory $category)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:vendor_categories,name,' . $category->id,
                'description' => 'nullable|string',
            ]);
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            return redirect()->route('admin.procurement.vendor-categories.index')->with('success', 'Vendor category updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, VendorCategory $category)
    {
        try {
            $category->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Vendor category deleted successfully!']);
            }
            return redirect()->route('admin.procurement.vendor-categories.index')->with('success', 'Vendor category deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, VendorCategory $category)
    {
        try {
            $category->status = $category->status === 'active' ? 'inactive' : 'active';
            $category->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $category->status, 'message' => 'Vendor category status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Vendor category status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
