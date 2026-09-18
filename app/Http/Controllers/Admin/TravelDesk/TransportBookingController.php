<?php

namespace App\Http\Controllers\Admin\TravelDesk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransportType;
use App\Models\TransportBooking;
use App\Models\Hotel;
use App\Models\Guest;
use App\Models\Room;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Carbon\Carbon;

class TransportBookingController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.travel-desk.bookings.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = TransportBooking::query()->with(['hotel', 'transportType']);

        return DataTables::of($query)
            ->filterColumn('booking_number', function ($query, $value) {
                $query->where('booking_number', 'like', "%{$value}%");
            })
            ->filterColumn('guest_name', function ($query, $value) {
                $query->where('guest_name', 'like', "%{$value}%");
            })
            ->filterColumn('type_name', function ($query, $value) {
                $query->whereHas('transportType', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('type_name', fn($b) => $b->transportType->name ?? '')
            ->addColumn('hotel_name', fn($b) => $b->hotel->name ?? '')
            ->addColumn('pickup_datetime_formatted', fn($b) => $b->pickup_datetime ? $b->pickup_datetime->format('d-m-Y H:i') : '')
            ->addColumn('trip_type_label', function ($row) {
                $labels = ['pickup' => 'Pickup', 'drop' => 'Drop', 'round_trip' => 'Round Trip', 'hourly' => 'Hourly'];
                return $labels[$row->trip_type] ?? ucfirst($row->trip_type);
            })
            ->addColumn('status_badge', function ($row) {
                $colors = ['pending' => 'warning', 'confirmed' => 'primary', 'in_progress' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->status)) . '</span>';
            })
            ->addColumn('payment_status_badge', function ($row) {
                $colors = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success'];
                $color = $colors[$row->payment_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->payment_status) . '</span>';
            })
            ->addColumn('status_url', fn($b) => route('admin.travel-desk.bookings.status', $b))
            ->addColumn('edit_url', fn($b) => route('admin.travel-desk.bookings.edit', $b))
            ->addColumn('delete_url', fn($b) => route('admin.travel-desk.bookings.destroy', $b))
            ->addColumn('show_url', fn($b) => route('admin.travel-desk.bookings.show', $b))
            ->rawColumns(['status_badge', 'payment_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $booking = null;
        $hotels = Hotel::where('status', 'active')->get();
        $types = TransportType::where('status', 'active')->get();
        $guests = Guest::where('status', 'active')->get();
        return view('admin.travel-desk.bookings.form', compact('booking', 'hotels', 'types', 'guests'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'transport_type_id' => 'required|exists:transport_types,id',
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_email' => 'nullable|email|max:255',
                'trip_type' => 'required|in:pickup,drop,round_trip,hourly',
                'pickup_location' => 'required|string|max:255',
                'drop_location' => 'required|string|max:255',
                'pickup_datetime' => 'required|date|after_or_equal:now',
                'estimated_distance_km' => 'nullable|numeric|min:0',
                'estimated_hours' => 'nullable|numeric|min:0',
                'additional_charges' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'advance_paid' => 'nullable|numeric|min:0',
                'driver_name' => 'nullable|string|max:255',
                'driver_phone' => 'nullable|string|max:20',
                'vehicle_number' => 'nullable|string|max:50',
                'special_instructions' => 'nullable|string',
                'is_room_charge' => 'nullable|in:yes,no',
            ]);

            $type = TransportType::find($request->transport_type_id);

            $basePrice = $type->base_price;
            $distanceCharges = ($request->estimated_distance_km ?? 0) * $type->per_km_rate;
            $hourlyCharges = ($request->estimated_hours ?? 0) * $type->per_hour_rate;
            $additional = $request->additional_charges ?? 0;
            $discount = $request->discount_amount ?? 0;
            $subtotal = $basePrice + $distanceCharges + $hourlyCharges + $additional - $discount;
            $tax = $request->tax_amount ?? 0;
            $total = $subtotal + $tax;
            $advance = $request->advance_paid ?? 0;

            $bookingNumber = 'TRV-' . Carbon::now()->format('Ymd') . '-' . str_pad(TransportBooking::count() + 1, 4, '0', STR_PAD_LEFT);

            TransportBooking::create([
                'hotel_id' => $request->hotel_id,
                'transport_type_id' => $request->transport_type_id,
                'guest_id' => $request->guest_id,
                'room_id' => $request->room_id,
                'booking_number' => $bookingNumber,
                'slug' => Helper::slug('transport_bookings', $bookingNumber),
                'guest_name' => $request->guest_name,
                'guest_phone' => $request->guest_phone,
                'guest_email' => $request->guest_email,
                'trip_type' => $request->trip_type,
                'pickup_location' => $request->pickup_location,
                'drop_location' => $request->drop_location,
                'pickup_datetime' => $request->pickup_datetime,
                'estimated_distance_km' => $request->estimated_distance_km,
                'estimated_hours' => $request->estimated_hours,
                'base_price' => $basePrice,
                'distance_charges' => $distanceCharges,
                'hourly_charges' => $hourlyCharges,
                'additional_charges' => $additional,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'advance_paid' => $advance,
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'vehicle_number' => $request->vehicle_number,
                'special_instructions' => $request->special_instructions,
                'status' => 'pending',
                'payment_status' => $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid'),
                'is_room_charge' => $request->input('is_room_charge', 'no'),
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('admin.travel-desk.bookings.index')->with('success', 'Transport booking created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(TransportBooking $booking)
    {
        $booking->load(['hotel', 'transportType', 'guest', 'room', 'createdBy']);
        return view('admin.travel-desk.bookings.show', compact('booking'));
    }

    public function tollPrint(TransportBooking $booking)
    {
        $booking->load(['hotel', 'transportType', 'guest', 'room', 'createdBy']);

        return view('admin.travel-desk.bookings.toll-print', compact('booking'));
    }

    public function edit(TransportBooking $booking)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $types = TransportType::where('status', 'active')->get();
        $guests = Guest::where('status', 'active')->get();
        return view('admin.travel-desk.bookings.form', compact('booking', 'hotels', 'types', 'guests'));
    }

    public function update(Request $request, TransportBooking $booking)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'transport_type_id' => 'required|exists:transport_types,id',
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_email' => 'nullable|email|max:255',
                'trip_type' => 'required|in:pickup,drop,round_trip,hourly',
                'pickup_location' => 'required|string|max:255',
                'drop_location' => 'required|string|max:255',
                'pickup_datetime' => 'required|date',
                'estimated_distance_km' => 'nullable|numeric|min:0',
                'estimated_hours' => 'nullable|numeric|min:0',
                'additional_charges' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'advance_paid' => 'nullable|numeric|min:0',
                'driver_name' => 'nullable|string|max:255',
                'driver_phone' => 'nullable|string|max:20',
                'vehicle_number' => 'nullable|string|max:50',
                'special_instructions' => 'nullable|string',
                'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
                'is_room_charge' => 'nullable|in:yes,no',
            ]);

            $type = TransportType::find($request->transport_type_id);

            $basePrice = $type->base_price;
            $distanceCharges = ($request->estimated_distance_km ?? 0) * $type->per_km_rate;
            $hourlyCharges = ($request->estimated_hours ?? 0) * $type->per_hour_rate;
            $additional = $request->additional_charges ?? 0;
            $discount = $request->discount_amount ?? 0;
            $subtotal = $basePrice + $distanceCharges + $hourlyCharges + $additional - $discount;
            $tax = $request->tax_amount ?? 0;
            $total = $subtotal + $tax;
            $advance = $request->advance_paid ?? 0;

            $booking->update([
                'hotel_id' => $request->hotel_id,
                'transport_type_id' => $request->transport_type_id,
                'guest_id' => $request->guest_id,
                'room_id' => $request->room_id,
                'guest_name' => $request->guest_name,
                'guest_phone' => $request->guest_phone,
                'guest_email' => $request->guest_email,
                'trip_type' => $request->trip_type,
                'pickup_location' => $request->pickup_location,
                'drop_location' => $request->drop_location,
                'pickup_datetime' => $request->pickup_datetime,
                'estimated_distance_km' => $request->estimated_distance_km,
                'estimated_hours' => $request->estimated_hours,
                'base_price' => $basePrice,
                'distance_charges' => $distanceCharges,
                'hourly_charges' => $hourlyCharges,
                'additional_charges' => $additional,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'advance_paid' => $advance,
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'vehicle_number' => $request->vehicle_number,
                'special_instructions' => $request->special_instructions,
                'status' => $request->status,
                'payment_status' => $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid'),
                'is_room_charge' => $request->input('is_room_charge', 'no'),
            ]);

            return redirect()->route('admin.travel-desk.bookings.index')->with('success', 'Transport booking updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, TransportBooking $booking)
    {
        try {
            $booking->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transport booking deleted successfully!']);
            }
            return redirect()->route('admin.travel-desk.bookings.index')->with('success', 'Transport booking deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, TransportBooking $booking)
    {
        try {
            $booking->status = $booking->status === 'confirmed' ? 'cancelled' : 'confirmed';
            $booking->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $booking->status, 'message' => 'Transport booking status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Transport booking status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
