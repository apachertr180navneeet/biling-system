<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Hotel;
use App\Models\RatePlan;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        $roomTypes = RoomType::where('status', 'active')->get();
        return view('admin.reservation.calendar.index', compact('hotels', 'roomTypes'));
    }

    public function events(Request $request)
    {
        $startDate = $request->input('start', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end', Carbon::now()->endOfMonth()->toDateString());
        $hotelId = $request->input('hotel_id');
        $roomTypeId = $request->input('room_type_id');

        $query = Reservation::query()
            ->with(['guest', 'hotel', 'rooms.room', 'rooms.roomType'])
            ->where('status', '!=', 'cancelled')
            ->where('check_out_date', '>=', $startDate)
            ->where('check_in_date', '<=', $endDate);

        if ($hotelId) {
            $query->where('hotel_id', $hotelId);
        }

        $reservations = $query->get();

        $events = [];
        foreach ($reservations as $reservation) {
            $color = match($reservation->status) {
                'pending' => '#f59e0b',
                'confirmed' => '#3b82f6',
                'checked-in' => '#10b981',
                'checked-out' => '#6b7280',
                'no-show' => '#ef4444',
                default => '#6b7280',
            };

            $events[] = [
                'id' => $reservation->id,
                'title' => $reservation->guest->full_name . ' (' . $reservation->reservation_number . ')',
                'start' => $reservation->check_in_date->toDateString(),
                'end' => $reservation->check_out_date->addDay()->toDateString(),
                'color' => $color,
                'extendedProps' => [
                    'reservation_number' => $reservation->reservation_number,
                    'guest_name' => $reservation->guest->full_name ?? '',
                    'hotel_name' => $reservation->hotel->name ?? '',
                    'status' => $reservation->status,
                    'booking_source' => $reservation->booking_source,
                    'adults' => $reservation->adults,
                    'children' => $reservation->children,
                    'total_amount' => $reservation->total_amount,
                    'rooms' => $reservation->rooms->map(fn($rr) => [
                        'room_number' => $rr->room->room_number ?? '',
                        'room_type' => $rr->roomType->name ?? '',
                        'rate' => $rr->rate_per_night,
                    ])->toArray(),
                ],
            ];
        }

        return response()->json($events);
    }

    public function availability(Request $request)
    {
        $date = $request->input('date', Carbon::now()->toDateString());
        $hotelId = $request->input('hotel_id');

        $query = Room::query()->with(['roomType', 'roomStatus'])
            ->where('status', 'active');

        if ($hotelId) {
            $query->where('hotel_id', $hotelId);
        }

        $rooms = $query->get();

        $bookedRoomIds = Reservation::query()
            ->where('status', '!=', 'cancelled')
            ->where('check_in_date', '<=', $date)
            ->where('check_out_date', '>', $date)
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->pluck('id');

        $bookedRoomIds = ReservationRoom::whereIn('reservation_id', $bookedRoomIds)
            ->pluck('room_id')
            ->toArray();

        $result = $rooms->map(function ($room) use ($bookedRoomIds) {
            return [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'room_type' => $room->roomType->name ?? '',
                'status' => $room->roomStatus->name ?? '',
                'status_color' => $room->roomStatus->color ?? '#6c757d',
                'is_available' => !in_array($room->id, $bookedRoomIds),
                'base_rate' => $room->roomType->base_rate ?? 0,
            ];
        });

        return response()->json($result);
    }

    public function rates(Request $request)
    {
        $hotelId = $request->input('hotel_id');
        $query = RatePlan::query()->with(['roomType']);

        if ($hotelId) {
            $query->where('hotel_id', $hotelId);
        }

        $rates = $query->where('status', 'active')
            ->where('effective_from', '<=', Carbon::now())
            ->where('effective_to', '>=', Carbon::now())
            ->get();

        return response()->json($rates);
    }
}
