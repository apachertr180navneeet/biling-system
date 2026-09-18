<?php

namespace App\Http\Controllers\Admin\Housekeeping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaundryOrder;
use App\Models\LaundryItem;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class LaundryOrderController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.housekeeping.laundry-orders.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = LaundryOrder::query()->with(['hotel']);
        return DataTables::of($query)
            ->filterColumn('order_number', function ($query, $value) {
                $query->where('order_number', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($c) => $c->hotel->name ?? '')
            ->addColumn('order_date_formatted', fn($c) => $c->order_date?->format('d-m-Y') ?? '')
            ->addColumn('order_status_badge', function ($row) {
                $colors = ['pending' => 'secondary', 'in_progress' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                $color = $colors[$row->order_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->order_status)) . '</span>';
            })
            ->addColumn('status_url', fn($c) => route('admin.housekeeping.laundry-orders.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.housekeeping.laundry-orders.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.housekeeping.laundry-orders.destroy', $c))
            ->rawColumns(['order_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $laundryOrder = null;
        $hotels = Hotel::where('status', 'active')->get();
        $laundryItems = LaundryItem::where('status', 'active')->get();
        return view('admin.housekeeping.laundry-orders.form', compact('laundryOrder', 'hotels', 'laundryItems'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'order_date' => 'required|date',
                'expected_return_date' => 'nullable|date|after_or_equal:order_date',
                'vendor_name' => 'nullable|string|max:255',
                'total_items' => 'required|integer|min:1',
                'total_weight' => 'nullable|numeric|min:0',
                'total_cost' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.laundry_item_id' => 'required|exists:laundry_items,id',
                'items.*.quantity_sent' => 'required|integer|min:1',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('laundry_orders', 'LO-' . date('YmdHis'));
            $data['order_number'] = 'LO-' . date('YmdHis');

            $order = LaundryOrder::create(collect($data)->only([
                'hotel_id', 'order_number', 'slug', 'order_date', 'expected_return_date',
                'vendor_name', 'total_items', 'total_weight', 'total_cost', 'notes', 'order_status', 'status'
            ])->toArray());

            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $order->items()->attach($item['laundry_item_id'], [
                        'quantity_sent' => $item['quantity_sent'],
                    ]);
                }
            }

            return redirect()->route('admin.housekeeping.laundry-orders.index')->with('success', 'Laundry Order created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(LaundryOrder $laundryOrder)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $laundryItems = LaundryItem::where('status', 'active')->get();
        $laundryOrder->load('items');
        return view('admin.housekeeping.laundry-orders.form', compact('laundryOrder', 'hotels', 'laundryItems'));
    }

    public function update(Request $request, LaundryOrder $laundryOrder)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'order_date' => 'required|date',
                'expected_return_date' => 'nullable|date|after_or_equal:order_date',
                'vendor_name' => 'nullable|string|max:255',
                'total_items' => 'required|integer|min:1',
                'total_weight' => 'nullable|numeric|min:0',
                'total_cost' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.laundry_item_id' => 'required|exists:laundry_items,id',
                'items.*.quantity_sent' => 'required|integer|min:1',
            ]);

            $laundryOrder->update(collect($request->all())->only([
                'hotel_id', 'order_date', 'expected_return_date',
                'vendor_name', 'total_items', 'total_weight', 'total_cost', 'notes', 'order_status', 'status'
            ])->toArray());

            $laundryOrder->items()->detach();
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $laundryOrder->items()->attach($item['laundry_item_id'], [
                        'quantity_sent' => $item['quantity_sent'],
                    ]);
                }
            }

            return redirect()->route('admin.housekeeping.laundry-orders.index')->with('success', 'Laundry Order updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, LaundryOrder $laundryOrder)
    {
        try {
            $laundryOrder->items()->detach();
            $laundryOrder->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Laundry Order deleted successfully!']);
            }
            return redirect()->route('admin.housekeeping.laundry-orders.index')->with('success', 'Laundry Order deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, LaundryOrder $laundryOrder)
    {
        try {
            $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
            $currentIndex = array_search($laundryOrder->order_status, $statuses);
            $nextIndex = ($currentIndex + 1) % count($statuses);
            $laundryOrder->order_status = $statuses[$nextIndex];

            if ($laundryOrder->order_status === 'completed') {
                $laundryOrder->actual_return_date = now()->toDateString();
            }

            $laundryOrder->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $laundryOrder->order_status, 'message' => 'Status updated successfully!']);
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
