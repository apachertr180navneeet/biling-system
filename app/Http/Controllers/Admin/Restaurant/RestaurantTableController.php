<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantTable;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class RestaurantTableController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.restaurant.tables.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = RestaurantTable::query()->with(['hotel']);
        return DataTables::of($query)
            ->filterColumn('table_number', function ($query, $value) {
                $query->where('table_number', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($c) => $c->hotel->name ?? '')
            ->addColumn('table_status_badge', function ($row) {
                $colors = ['available' => 'success', 'occupied' => 'danger', 'reserved' => 'info', 'maintenance' => 'warning'];
                $color = $colors[$row->table_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->table_status) . '</span>';
            })
            ->addColumn('status_url', fn($c) => route('admin.restaurant.tables.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.restaurant.tables.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.restaurant.tables.destroy', $c))
            ->rawColumns(['table_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $restaurantTable = null;
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.restaurant.tables.form', compact('restaurantTable', 'hotels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'table_number' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
                'floor_number' => 'nullable|integer|min:0',
                'section' => 'nullable|string|max:255',
                'table_status' => 'required|in:available,occupied,reserved,maintenance',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('restaurant_tables', $request->table_number);
            RestaurantTable::create($data);
            return redirect()->route('admin.restaurant.tables.index')->with('success', 'Restaurant Table created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RestaurantTable $restaurantTable)
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.restaurant.tables.form', compact('restaurantTable', 'hotels'));
    }

    public function update(Request $request, RestaurantTable $restaurantTable)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'table_number' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
                'floor_number' => 'nullable|integer|min:0',
                'section' => 'nullable|string|max:255',
                'table_status' => 'required|in:available,occupied,reserved,maintenance',
            ]);
            $restaurantTable->update($request->all());
            return redirect()->route('admin.restaurant.tables.index')->with('success', 'Restaurant Table updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, RestaurantTable $restaurantTable)
    {
        try {
            $restaurantTable->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Restaurant Table deleted successfully!']);
            }
            return redirect()->route('admin.restaurant.tables.index')->with('success', 'Restaurant Table deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, RestaurantTable $restaurantTable)
    {
        try {
            $statuses = ['available', 'occupied', 'reserved', 'maintenance'];
            $currentIndex = array_search($restaurantTable->table_status, $statuses);
            $nextIndex = ($currentIndex + 1) % count($statuses);
            $restaurantTable->table_status = $statuses[$nextIndex];
            $restaurantTable->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $restaurantTable->table_status, 'message' => 'Status updated successfully!']);
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
