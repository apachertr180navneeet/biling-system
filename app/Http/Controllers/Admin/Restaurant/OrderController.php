<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\RestaurantTable;
use App\Models\RestaurantMenuItem;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class OrderController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.restaurant.orders.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = RestaurantOrder::query()->with(['hotel', 'restaurantTable', 'createdBy']);
        return DataTables::of($query)
            ->filterColumn('order_number', function ($query, $value) {
                $query->where('order_number', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('guest_name', function ($query, $value) {
                $query->where('guest_name', 'like', "%{$value}%");
            })
            ->addColumn('hotel_name', fn($c) => $c->hotel->name ?? '')
            ->addColumn('table_number', fn($c) => $c->restaurantTable->table_number ?? 'N/A')
            ->addColumn('created_by_name', fn($c) => $c->createdBy ? $c->createdBy->first_name . ' ' . $c->createdBy->last_name : '')
            ->addColumn('order_type_badge', function ($row) {
                $colors = ['dine_in' => 'primary', 'takeaway' => 'info', 'room_service' => 'warning'];
                $color = $colors[$row->order_type] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->order_type)) . '</span>';
            })
            ->addColumn('order_status_badge', function ($row) {
                $colors = ['pending' => 'secondary', 'preparing' => 'info', 'ready' => 'warning', 'served' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'];
                $color = $colors[$row->order_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->order_status) . '</span>';
            })
            ->addColumn('payment_status_badge', function ($row) {
                $colors = ['pending' => 'warning', 'paid' => 'success', 'partially_paid' => 'info'];
                $color = $colors[$row->payment_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->payment_status)) . '</span>';
            })
            ->addColumn('status_url', fn($c) => route('admin.restaurant.orders.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.restaurant.orders.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.restaurant.orders.destroy', $c))
            ->rawColumns(['order_type_badge', 'order_status_badge', 'payment_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $order = null;
        $hotels = Hotel::where('status', 'active')->get();
        $tables = RestaurantTable::where('table_status', 'available')->where('status', 'active')->get();
        $menuItems = RestaurantMenuItem::where('is_available', true)->where('status', 'active')->get();
        $reservations = Reservation::whereIn('status', ['confirmed', 'checked-in'])->with(['guest'])->get();
        return view('admin.restaurant.orders.form', compact('order', 'hotels', 'tables', 'menuItems', 'reservations'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'restaurant_table_id' => 'nullable|exists:restaurant_tables,id',
                'reservation_id' => 'nullable|exists:reservations,id',
                'guest_name' => 'nullable|string|max:255',
                'order_type' => 'required|in:dine_in,takeaway,room_service',
                'total_amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'net_amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string|max:255',
                'payment_status' => 'required|in:pending,paid,partially_paid',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.restaurant_menu_item_id' => 'required|exists:restaurant_menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.special_instructions' => 'nullable|string',
            ]);

            $data = $request->all();
            $data['slug'] = Helper::slug('restaurant_orders', 'RO-' . date('YmdHis'));
            $data['order_number'] = 'RO-' . date('YmdHis');
            $data['created_by'] = auth()->id();

            $order = RestaurantOrder::create(collect($data)->only([
                'hotel_id', 'order_number', 'slug', 'restaurant_table_id', 'reservation_id',
                'guest_name', 'order_type', 'total_amount', 'tax_amount', 'discount_amount',
                'net_amount', 'payment_method', 'payment_status', 'notes', 'order_status', 'created_by', 'status'
            ])->toArray());

            foreach ($request->items as $item) {
                $order->items()->create([
                    'restaurant_menu_item_id' => $item['restaurant_menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);
            }

            if ($request->restaurant_table_id) {
                RestaurantTable::where('id', $request->restaurant_table_id)->update(['table_status' => 'occupied']);
            }

            return redirect()->route('admin.restaurant.orders.index')->with('success', 'Order created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RestaurantOrder $order)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $tables = RestaurantTable::where('status', 'active')->get();
        $menuItems = RestaurantMenuItem::where('status', 'active')->get();
        $reservations = Reservation::whereIn('status', ['confirmed', 'checked-in'])->with(['guest'])->get();
        $order->load('items');
        return view('admin.restaurant.orders.form', compact('order', 'hotels', 'tables', 'menuItems', 'reservations'));
    }

    public function update(Request $request, RestaurantOrder $order)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'restaurant_table_id' => 'nullable|exists:restaurant_tables,id',
                'reservation_id' => 'nullable|exists:reservations,id',
                'guest_name' => 'nullable|string|max:255',
                'order_type' => 'required|in:dine_in,takeaway,room_service',
                'total_amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'net_amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string|max:255',
                'payment_status' => 'required|in:pending,paid,partially_paid',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.restaurant_menu_item_id' => 'required|exists:restaurant_menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.special_instructions' => 'nullable|string',
            ]);

            $order->update(collect($request->all())->only([
                'hotel_id', 'restaurant_table_id', 'reservation_id', 'guest_name', 'order_type',
                'total_amount', 'tax_amount', 'discount_amount', 'net_amount', 'payment_method',
                'payment_status', 'notes', 'order_status', 'status'
            ])->toArray());

            $order->items()->delete();
            foreach ($request->items as $item) {
                $order->items()->create([
                    'restaurant_menu_item_id' => $item['restaurant_menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);
            }

            return redirect()->route('admin.restaurant.orders.index')->with('success', 'Order updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, RestaurantOrder $order)
    {
        try {
            if ($order->restaurant_table_id) {
                RestaurantTable::where('id', $order->restaurant_table_id)->update(['table_status' => 'available']);
            }
            $order->items()->delete();
            $order->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Order deleted successfully!']);
            }
            return redirect()->route('admin.restaurant.orders.index')->with('success', 'Order deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, RestaurantOrder $order)
    {
        try {
            $statuses = ['pending', 'preparing', 'ready', 'served', 'completed', 'cancelled'];
            $currentIndex = array_search($order->order_status, $statuses);
            $nextIndex = ($currentIndex + 1) % count($statuses);
            $order->order_status = $statuses[$nextIndex];

            if ($order->order_status === 'completed' && $order->restaurant_table_id) {
                RestaurantTable::where('id', $order->restaurant_table_id)->update(['table_status' => 'available']);
            }

            $order->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $order->order_status, 'message' => 'Status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function menuItems(Request $request)
    {
        $menuItems = RestaurantMenuItem::where('is_available', true)
            ->where('status', 'active')
            ->when($request->hotel_id, fn($q) => $q->where('hotel_id', $request->hotel_id))
            ->get();

        return response()->json($menuItems);
    }
}
