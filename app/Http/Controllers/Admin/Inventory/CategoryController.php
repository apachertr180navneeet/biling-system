<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryCategory;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.inventory.categories.index');
    }

    public function data(Request $request)
    {
        $query = InventoryCategory::query();
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($category) => route('admin.inventory.categories.status', $category))
            ->addColumn('edit_url', fn($category) => route('admin.inventory.categories.edit', $category))
            ->addColumn('delete_url', fn($category) => route('admin.inventory.categories.destroy', $category))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $category = null;
        return view('admin.inventory.categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('inventory_categories', $request->name);
            InventoryCategory::create($data);
            return redirect()->route('admin.inventory.categories.index')->with('success', 'Category created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(InventoryCategory $category)
    {
        return view('admin.inventory.categories.form', compact('category'));
    }

    public function update(Request $request, InventoryCategory $category)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);
            $category->update($request->all());
            return redirect()->route('admin.inventory.categories.index')->with('success', 'Category updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, InventoryCategory $category)
    {
        try {
            $category->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Category deleted successfully!']);
            }
            return redirect()->route('admin.inventory.categories.index')->with('success', 'Category deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, InventoryCategory $category)
    {
        try {
            $category->status = $category->status === 'active' ? 'inactive' : 'active';
            $category->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $category->status, 'message' => 'Category status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Category status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
