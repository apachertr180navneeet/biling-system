<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantMenuItem;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class MenuItemController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.restaurant.menu-items.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = RestaurantMenuItem::query()->with(['hotel']);
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($c) => $c->hotel->name ?? '')
            ->addColumn('category_badge', function ($row) {
                $colors = ['appetizer' => 'info', 'main_course' => 'primary', 'dessert' => 'warning', 'beverage' => 'success', 'special' => 'danger'];
                $color = $colors[$row->category] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->category)) . '</span>';
            })
            ->addColumn('status_url', fn($c) => route('admin.restaurant.menu-items.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.restaurant.menu-items.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.restaurant.menu-items.destroy', $c))
            ->rawColumns(['category_badge'])
            ->make(true);
    }

    public function create()
    {
        $menuItem = null;
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.restaurant.menu-items.form', compact('menuItem', 'hotels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'category' => 'required|in:appetizer,main_course,dessert,beverage,special',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'preparation_time' => 'nullable|integer|min:0',
                'is_available' => 'boolean',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('restaurant_menu_items', $request->name);
            $data['is_available'] = $request->boolean('is_available');
            RestaurantMenuItem::create($data);
            return redirect()->route('admin.restaurant.menu-items.index')->with('success', 'Menu Item created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RestaurantMenuItem $menuItem)
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.restaurant.menu-items.form', compact('menuItem', 'hotels'));
    }

    public function update(Request $request, RestaurantMenuItem $menuItem)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'category' => 'required|in:appetizer,main_course,dessert,beverage,special',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'preparation_time' => 'nullable|integer|min:0',
                'is_available' => 'boolean',
            ]);
            $data = $request->all();
            $data['is_available'] = $request->boolean('is_available');
            $menuItem->update($data);
            return redirect()->route('admin.restaurant.menu-items.index')->with('success', 'Menu Item updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, RestaurantMenuItem $menuItem)
    {
        try {
            $menuItem->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Menu Item deleted successfully!']);
            }
            return redirect()->route('admin.restaurant.menu-items.index')->with('success', 'Menu Item deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, RestaurantMenuItem $menuItem)
    {
        try {
            $menuItem->status = $menuItem->status === 'active' ? 'inactive' : 'active';
            $menuItem->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $menuItem->status, 'message' => 'Status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
