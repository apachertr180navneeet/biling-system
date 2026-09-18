<?php

namespace App\Http\Controllers\Admin\Housekeeping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaundryItem;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class LaundryItemController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.housekeeping.laundry-items.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = LaundryItem::query()->with(['hotel']);
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
            ->addColumn('item_type_badge', function ($row) {
                $colors = ['linen' => 'primary', 'towel' => 'info', 'uniform' => 'warning', 'other' => 'secondary'];
                $color = $colors[$row->item_type] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->item_type) . '</span>';
            })
            ->addColumn('status_url', fn($c) => route('admin.housekeeping.laundry-items.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.housekeeping.laundry-items.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.housekeeping.laundry-items.destroy', $c))
            ->rawColumns(['item_type_badge'])
            ->make(true);
    }

    public function create()
    {
        $laundryItem = null;
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.housekeeping.laundry-items.form', compact('laundryItem', 'hotels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'item_type' => 'required|in:linen,towel,uniform,other',
                'quantity' => 'required|integer|min:0',
                'unit' => 'required|in:pieces,kg,pairs',
                'description' => 'nullable|string',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('laundry_items', $request->name);
            LaundryItem::create($data);
            return redirect()->route('admin.housekeeping.laundry-items.index')->with('success', 'Laundry Item created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(LaundryItem $laundryItem)
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.housekeeping.laundry-items.form', compact('laundryItem', 'hotels'));
    }

    public function update(Request $request, LaundryItem $laundryItem)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'item_type' => 'required|in:linen,towel,uniform,other',
                'quantity' => 'required|integer|min:0',
                'unit' => 'required|in:pieces,kg,pairs',
                'description' => 'nullable|string',
            ]);
            $laundryItem->update($request->all());
            return redirect()->route('admin.housekeeping.laundry-items.index')->with('success', 'Laundry Item updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, LaundryItem $laundryItem)
    {
        try {
            $laundryItem->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Laundry Item deleted successfully!']);
            }
            return redirect()->route('admin.housekeeping.laundry-items.index')->with('success', 'Laundry Item deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, LaundryItem $laundryItem)
    {
        try {
            $laundryItem->status = $laundryItem->status === 'active' ? 'inactive' : 'active';
            $laundryItem->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $laundryItem->status, 'message' => 'Status updated successfully!']);
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
