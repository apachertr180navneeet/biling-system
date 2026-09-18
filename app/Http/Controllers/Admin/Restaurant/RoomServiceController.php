<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomServiceCharge;
use App\Models\RestaurantOrder;
use App\Models\Reservation;
use App\Models\Hotel;
use App\Models\RoomStatus;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class RoomServiceController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.restaurant.room-service.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = RoomServiceCharge::query()->with(['hotel', 'reservation', 'restaurantOrder', 'postedBy']);
        return DataTables::of($query)
            ->filterColumn('charge_number', function ($query, $value) {
                $query->where('charge_number', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('guest_name', function ($query, $value) {
                $query->whereHas('reservation', function ($q) use ($value) {
                    $q->whereHas('guest', function ($q2) use ($value) {
                        $q2->where('first_name', 'like', "%{$value}%")
                            ->orWhere('last_name', 'like', "%{$value}%");
                    });
                });
            })
            ->addColumn('hotel_name', fn($c) => $c->hotel->name ?? '')
            ->addColumn('reservation_number', fn($c) => $c->reservation->reservation_number ?? '')
            ->addColumn('guest_name', fn($c) => $c->reservation->guest->full_name ?? '')
            ->addColumn('order_number', fn($c) => $c->restaurantOrder->order_number ?? 'N/A')
            ->addColumn('posted_by_name', fn($c) => $c->postedBy ? $c->postedBy->first_name . ' ' . $c->postedBy->last_name : '')
            ->addColumn('posted_at_formatted', fn($c) => $c->posted_at?->format('d-m-Y H:i') ?? '')
            ->addColumn('charge_status_badge', function ($row) {
                $colors = ['posted' => 'success', 'voided' => 'danger'];
                $color = $colors[$row->charge_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->charge_status) . '</span>';
            })
            ->addColumn('status_url', fn($c) => route('admin.restaurant.room-service.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.restaurant.room-service.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.restaurant.room-service.destroy', $c))
            ->rawColumns(['charge_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $charge = null;
        $hotels = Hotel::where('status', 'active')->get();
        $reservations = Reservation::whereIn('status', ['confirmed', 'checked-in'])->with(['guest', 'hotel'])->get();
        $orders = RestaurantOrder::where('order_type', 'room_service')
            ->whereIn('order_status', ['completed', 'served'])
            ->get();
        return view('admin.restaurant.room-service.form', compact('charge', 'hotels', 'reservations', 'orders'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'reservation_id' => 'required|exists:reservations,id',
                'restaurant_order_id' => 'nullable|exists:restaurant_orders,id',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('room_service_charges', 'RSC-' . date('YmdHis'));
            $data['charge_number'] = 'RSC-' . date('YmdHis');
            $data['posted_by'] = auth()->id();
            $data['posted_at'] = now();

            RoomServiceCharge::create(collect($data)->only([
                'hotel_id', 'reservation_id', 'restaurant_order_id', 'charge_number',
                'slug', 'amount', 'tax_amount', 'total_amount', 'posted_by',
                'posted_at', 'notes', 'charge_status', 'status'
            ])->toArray());

            return redirect()->route('admin.restaurant.room-service.index')->with('success', 'Room Service Charge posted to folio successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RoomServiceCharge $charge)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $reservations = Reservation::whereIn('status', ['confirmed', 'checked-in'])->with(['guest', 'hotel'])->get();
        $orders = RestaurantOrder::where('order_type', 'room_service')
            ->whereIn('order_status', ['completed', 'served'])
            ->get();
        return view('admin.restaurant.room-service.form', compact('charge', 'hotels', 'reservations', 'orders'));
    }

    public function update(Request $request, RoomServiceCharge $charge)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'reservation_id' => 'required|exists:reservations,id',
                'restaurant_order_id' => 'nullable|exists:restaurant_orders,id',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);
            $charge->update(collect($request->all())->only([
                'hotel_id', 'reservation_id', 'restaurant_order_id',
                'amount', 'tax_amount', 'total_amount', 'notes', 'charge_status', 'status'
            ])->toArray());
            return redirect()->route('admin.restaurant.room-service.index')->with('success', 'Room Service Charge updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, RoomServiceCharge $charge)
    {
        try {
            $charge->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Room Service Charge deleted successfully!']);
            }
            return redirect()->route('admin.restaurant.room-service.index')->with('success', 'Room Service Charge deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, RoomServiceCharge $charge)
    {
        try {
            $charge->charge_status = $charge->charge_status === 'posted' ? 'voided' : 'posted';
            $charge->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $charge->charge_status, 'message' => 'Status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function ordersByReservation(Request $request)
    {
        $orders = RestaurantOrder::where('reservation_id', $request->reservation_id)
            ->where('order_type', 'room_service')
            ->whereIn('order_status', ['completed', 'served'])
            ->get();

        return response()->json($orders);
    }
}
