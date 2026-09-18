<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomStatus;
use App\Models\RatePlan;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\ReservationRoom;

use App\Models\Tax;

class BookingController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();

        return view('web.booking.index', [
            'hotels' => $hotels,
        ]);
    }

    public function availability(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
        ]);

        $hotel = Hotel::with(['rooms.roomType', 'rooms.bedType', 'rooms.roomStatus'])->findOrFail($request->hotel_id);

        $bookedRoomIds = ReservationRoom::whereHas('reservation', function ($q) use ($request) {
            $q->whereIn('status', ['pending', 'confirmed'])
              ->where('check_in_date', '<', $request->check_out)
              ->where('check_out_date', '>', $request->check_in);
        })->pluck('room_id')->toArray();

        $availableRooms = $hotel->rooms()
            ->where('status', 'active')
            ->whereNotIn('id', $bookedRoomIds)
            ->with(['roomType', 'bedType'])
            ->get();

        $ratePlans = RatePlan::where('hotel_id', $hotel->id)
            ->where('status', 'active')
            ->where('effective_from', '<=', $request->check_out)
            ->where('effective_to', '>=', $request->check_in)
            ->get()
            ->keyBy('room_type_id');

        $nights = max(1, \Carbon\Carbon::parse($request->check_in)->diffInDays($request->check_out));

        $roomsWithRates = $availableRooms->map(function ($room) use ($ratePlans, $nights) {
            $ratePlan = $ratePlans->get($room->room_type_id);
            $ratePerNight = $ratePlan ? $ratePlan->rate_per_night : ($room->roomType->base_rate ?? 0);

            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'type_name' => $room->roomType->name ?? 'Standard',
                'bed_type' => $room->bedType->name ?? '',
                'max_occupancy' => $room->roomType->max_occupancy ?? 2,
                'rate_per_night' => (float) $ratePerNight,
                'total_rate' => (float) $ratePerNight * $nights,
            ];
        });

        return response()->json([
            'success' => true,
            'rooms' => $roomsWithRates,
            'nights' => $nights,
            'currency' => $this->currencySymbol(),
            'tax_rate' => $this->taxRate(),
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
        ]);

        $hotel = Hotel::findOrFail($request->hotel_id);
        $room = Room::with(['roomType', 'bedType'])->findOrFail($request->room_id);

        $ratePlan = RatePlan::where('hotel_id', $hotel->id)
            ->where('room_type_id', $room->room_type_id)
            ->where('status', 'active')
            ->first();

        $taxRate = $this->taxRate();
        $nights = max(1, \Carbon\Carbon::parse($request->check_in)->diffInDays($request->check_out));
        $ratePerNight = $ratePlan ? $ratePlan->rate_per_night : ($room->roomType->base_rate ?? 0);
        $subtotal = (float) $ratePerNight * $nights;
        $tax = round($subtotal * $taxRate / 100);
        $total = $subtotal + $tax;

        return view('web.booking.checkout', [
            'hotel' => $hotel,
            'room' => $room,
            'checkIn' => $request->check_in,
            'checkOut' => $request->check_out,
            'adults' => $request->adults,
            'nights' => $nights,
            'ratePerNight' => $ratePerNight,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'taxRate' => $taxRate,
            'total' => $total,
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $hotel = Hotel::findOrFail($request->hotel_id);
        $room = Room::with(['roomType'])->findOrFail($request->room_id);

        $guest = Guest::firstOrCreate(
            ['email' => $request->email],
            [
                'first_name' => $request->first_name,
                'phone' => $request->phone ?? '',
                'slug' => \Illuminate\Support\Str::slug($request->first_name),
                'status' => 'active',
            ]
        );

        $ratePlan = RatePlan::where('hotel_id', $hotel->id)
            ->where('room_type_id', $room->room_type_id)
            ->where('status', 'active')
            ->first();

        $taxRate = $this->taxRate();
        $nights = max(1, \Carbon\Carbon::parse($request->check_in)->diffInDays($request->check_out));
        $ratePerNight = $ratePlan ? $ratePlan->rate_per_night : ($room->roomType->base_rate ?? 0);
        $subtotal = (float) $ratePerNight * $nights;
        $tax = round($subtotal * $taxRate / 100);

        $reservation = Reservation::create([
            'reservation_number' => Reservation::generateNumber(),
            'hotel_id' => $hotel->id,
            'guest_id' => $guest->id,
            'booking_source' => 'online',
            'check_in_date' => $request->check_in,
            'check_out_date' => $request->check_out,
            'adults' => $request->adults,
            'total_amount' => $subtotal + $tax,
            'status' => 'pending',
            'created_by' => null,
        ]);

        ReservationRoom::create([
            'reservation_id' => $reservation->id,
            'room_id' => $room->id,
            'room_type_id' => $room->room_type_id,
            'rate_plan_id' => $ratePlan?->id,
            'check_in_date' => $request->check_in,
            'check_out_date' => $request->check_out,
            'rate_per_night' => $ratePerNight,
            'total_amount' => $subtotal,
            'status' => 'pending',
        ]);

        $reservation->update(['total_amount' => $subtotal + $tax]);

        return view('web.booking.confirmation', [
            'reservation' => $reservation,
            'hotel' => $hotel,
            'room' => $room,
            'guest' => $guest,
            'nights' => $nights,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'taxRate' => $taxRate,
            'total' => $subtotal + $tax,
        ]);
    }

    private function currencySymbol(): string
    {
        $companyCurrency = Company::with('currency')->first()?->currency;
        if ($companyCurrency?->status === 'active') {
            return $companyCurrency->symbol;
        }
        return Currency::where('is_default', true)
            ->where('status', 'active')
            ->value('symbol') ?? '$';
    }

    private function taxRate(): float
    {
        $defaultTax = Tax::where('is_default', true)
            ->where('status', 'active')
            ->where('type', 'percentage')
            ->first();

        return $defaultTax ? (float) $defaultTax->rate : 12.0;
    }
}
