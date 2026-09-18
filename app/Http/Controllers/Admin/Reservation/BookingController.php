<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\ReservationPayment;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RatePlan;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class BookingController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.reservation.bookings.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = Reservation::query()->with(['hotel', 'guest']);
        return DataTables::of($query)
            ->filterColumn('reservation_number', function ($query, $value) {
                $query->where('reservation_number', 'like', "%{$value}%");
            })
            ->filterColumn('guest_name', function ($query, $value) {
                $query->whereHas('guest', function ($q) use ($value) {
                    $q->where('first_name', 'like', "%{$value}%")
                        ->orWhere('last_name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('guest_name', fn($r) => $r->guest->full_name ?? '')
            ->addColumn('hotel_name', fn($r) => $r->hotel->name ?? '')
            ->addColumn('nights', fn($r) => $r->nights)
            ->addColumn('balance', fn($r) => number_format($r->balance, 2))
            ->addColumn('status_url', fn($r) => route('admin.reservation.bookings.status', $r))
            ->addColumn('edit_url', fn($r) => route('admin.reservation.bookings.edit', $r))
            ->addColumn('delete_url', fn($r) => route('admin.reservation.bookings.destroy', $r))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $reservation = null;
        $hotels = Hotel::where('status', 'active')->get();
        $guests = Guest::where('status', 'active')->get();
        $rooms = Room::where('status', 'active')->get();
        $roomTypes = RoomType::where('status', 'active')->get();
        return view('admin.reservation.bookings.form', compact('reservation', 'hotels', 'guests', 'rooms', 'roomTypes'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required',
                'guest_id' => 'required',
                'booking_source' => 'required|in:walk-in,online,ota',
                'check_in_date' => 'required|date',
                'check_out_date' => 'required|date|after:check_in_date',
                'adults' => 'required|integer|min:1',
            ]);

            $data = $request->all();
            $data['reservation_number'] = Reservation::generateNumber();
            $data['created_by'] = auth()->id();

            $reservation = Reservation::create($data);

            $this->createReservationRooms($reservation, $request);

            return redirect()->route('admin.reservation.bookings.index')->with('success', 'Booking created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Reservation $reservation)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $guests = Guest::where('status', 'active')->get();
        $rooms = Room::where('status', 'active')->get();
        $roomTypes = RoomType::where('status', 'active')->get();
        $reservation->load('rooms');
        return view('admin.reservation.bookings.form', compact('reservation', 'hotels', 'guests', 'rooms', 'roomTypes'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        try {
            $request->validate([
                'hotel_id' => 'required',
                'guest_id' => 'required',
                'booking_source' => 'required|in:walk-in,online,ota',
                'check_in_date' => 'required|date',
                'check_out_date' => 'required|date|after:check_in_date',
                'adults' => 'required|integer|min:1',
            ]);

            $reservation->update($request->except(['reservation_number', 'created_by']));

            $reservation->rooms()->delete();
            $this->createReservationRooms($reservation, $request);

            return redirect()->route('admin.reservation.bookings.index')->with('success', 'Booking updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        try {
            $reservation->rooms()->delete();
            $reservation->payments()->delete();
            $reservation->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Booking deleted successfully!']);
            }
            return redirect()->route('admin.reservation.bookings.index')->with('success', 'Booking deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Reservation $reservation)
    {
        try {
            $statusFlow = [
                'pending' => 'confirmed',
                'confirmed' => 'checked-in',
                'checked-in' => 'checked-out',
            ];
            $current = $reservation->status;
            if (isset($statusFlow[$current])) {
                $reservation->status = $statusFlow[$current];
                if ($reservation->status === 'checked-in') {
                    $reservation->actual_check_in = now();
                } elseif ($reservation->status === 'checked-out') {
                    $reservation->actual_check_out = now();
                }
                $reservation->save();
            }
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $reservation->status, 'message' => 'Status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        try {
            $reservation->status = 'cancelled';
            $reservation->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $reservation->status, 'message' => 'Booking cancelled successfully!']);
            }
            return redirect()->back()->with('success', 'Booking cancelled successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    private function createReservationRooms(Reservation $reservation, Request $request)
    {
        $roomIds = $request->input('room_ids', []);
        $roomTypeIds = $request->input('room_type_ids', []);
        $rates = $request->input('rates', []);

        if (empty($roomIds)) {
            $nights = $reservation->check_in_date->diffInDays($reservation->check_out_date);
            $roomType = RoomType::first();
            $rate = $roomType ? $roomType->base_rate : 0;
            ReservationRoom::create([
                'reservation_id' => $reservation->id,
                'room_id' => $request->input('room_id', 1),
                'room_type_id' => $request->input('room_type_id', $roomType?->id),
                'check_in_date' => $reservation->check_in_date,
                'check_out_date' => $reservation->check_out_date,
                'rate_per_night' => $rate,
                'total_amount' => $rate * max($nights, 1),
                'status' => $reservation->status,
            ]);
        } else {
            foreach ($roomIds as $index => $roomId) {
                $nights = $reservation->check_in_date->diffInDays($reservation->check_out_date);
                $rate = $rates[$index] ?? 0;
                ReservationRoom::create([
                    'reservation_id' => $reservation->id,
                    'room_id' => $roomId,
                    'room_type_id' => $roomTypeIds[$index] ?? null,
                    'check_in_date' => $reservation->check_in_date,
                    'check_out_date' => $reservation->check_out_date,
                    'rate_per_night' => $rate,
                    'total_amount' => $rate * max($nights, 1),
                    'status' => $reservation->status,
                ]);
            }
        }

        $total = $reservation->rooms()->sum('total_amount');
        $reservation->update(['total_amount' => $total]);
    }
}
