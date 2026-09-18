<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\InventoryUnit;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class ItemController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        $categories = InventoryCategory::where('status', 'active')->get();
        $units = InventoryUnit::where('status', 'active')->get();
        return view('admin.inventory.items.index', compact('hotels', 'categories', 'units'));
    }

    public function data(Request $request)
    {
        $query = InventoryItem::query()->with(['category', 'unit', 'hotel']);
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('category_name', function ($query, $value) {
                $query->whereHas('category', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($item) => $item->hotel->name ?? '')
            ->addColumn('category_name', fn($item) => $item->category->name ?? '')
            ->addColumn('unit_short_name', fn($item) => $item->unit->short_name ?? '')
            ->addColumn('status_url', fn($item) => route('admin.inventory.items.status', $item))
            ->addColumn('edit_url', fn($item) => route('admin.inventory.items.edit', $item))
            ->addColumn('delete_url', fn($item) => route('admin.inventory.items.destroy', $item))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $item = null;
        $hotels = Hotel::where('status', 'active')->get();
        $categories = InventoryCategory::where('status', 'active')->get();
        $units = InventoryUnit::where('status', 'active')->get();
        return view('admin.inventory.items.form', compact('item', 'hotels', 'categories', 'units'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'category_id' => 'required|exists:inventory_categories,id',
                'unit_id' => 'required|exists:inventory_units,id',
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'cost_price' => 'required|numeric|min:0',
                'sell_price' => 'required|numeric|min:0',
                'min_stock' => 'required|integer|min:0',
                'max_stock' => 'required|integer|min:0',
                'is_reorder' => 'nullable|boolean',
                'status' => 'required|in:active,inactive',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('inventory_items', $request->name);
            $data['is_reorder'] = $request->boolean('is_reorder');
            InventoryItem::create($data);
            return redirect()->route('admin.inventory.items.index')->with('success', 'Item created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(InventoryItem $item)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $categories = InventoryCategory::where('status', 'active')->get();
        $units = InventoryUnit::where('status', 'active')->get();
        return view('admin.inventory.items.form', compact('item', 'hotels', 'categories', 'units'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'category_id' => 'required|exists:inventory_categories,id',
                'unit_id' => 'required|exists:inventory_units,id',
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'cost_price' => 'required|numeric|min:0',
                'sell_price' => 'required|numeric|min:0',
                'min_stock' => 'required|integer|min:0',
                'max_stock' => 'required|integer|min:0',
                'is_reorder' => 'nullable|boolean',
                'status' => 'required|in:active,inactive',
            ]);
            $data = $request->all();
            $data['is_reorder'] = $request->boolean('is_reorder');
            $item->update($data);
            return redirect()->route('admin.inventory.items.index')->with('success', 'Item updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, InventoryItem $item)
    {
        try {
            $item->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Item deleted successfully!']);
            }
            return redirect()->route('admin.inventory.items.index')->with('success', 'Item deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, InventoryItem $item)
    {
        try {
            $item->status = $item->status === 'active' ? 'inactive' : 'active';
            $item->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $item->status, 'message' => 'Item status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Item status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
