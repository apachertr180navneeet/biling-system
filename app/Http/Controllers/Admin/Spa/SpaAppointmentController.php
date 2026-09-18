<?php

namespace App\Http\Controllers\Admin\Spa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpaService;
use App\Models\SpaAppointment;
use App\Models\Hotel;
use App\Models\Guest;
use App\Models\Room;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Carbon\Carbon;

class SpaAppointmentController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.spa.appointments.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = SpaAppointment::query()->with(['hotel', 'spaService']);

        return DataTables::of($query)
            ->filterColumn('appointment_number', function ($query, $value) {
                $query->where('appointment_number', 'like', "%{$value}%");
            })
            ->filterColumn('guest_name', function ($query, $value) {
                $query->where('guest_name', 'like', "%{$value}%");
            })
            ->filterColumn('service_name', function ($query, $value) {
                $query->whereHas('spaService', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('service_name', fn($a) => $a->spaService->name ?? '')
            ->addColumn('hotel_name', fn($a) => $a->hotel->name ?? '')
            ->addColumn('appointment_date_formatted', fn($a) => $a->appointment_date ? $a->appointment_date->format('d-m-Y') : '')
            ->addColumn('status_badge', function ($row) {
                $colors = ['pending' => 'warning', 'confirmed' => 'primary', 'in_progress' => 'info', 'completed' => 'success', 'cancelled' => 'danger', 'no_show' => 'secondary'];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->status)) . '</span>';
            })
            ->addColumn('payment_status_badge', function ($row) {
                $colors = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success'];
                $color = $colors[$row->payment_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->payment_status) . '</span>';
            })
            ->addColumn('status_url', fn($a) => route('admin.spa.appointments.status', $a))
            ->addColumn('edit_url', fn($a) => route('admin.spa.appointments.edit', $a))
            ->addColumn('delete_url', fn($a) => route('admin.spa.appointments.destroy', $a))
            ->addColumn('show_url', fn($a) => route('admin.spa.appointments.show', $a))
            ->rawColumns(['status_badge', 'payment_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $appointment = null;
        $hotels = Hotel::where('status', 'active')->get();
        $services = SpaService::where('status', 'active')->get();
        $guests = Guest::where('status', 'active')->get();
        return view('admin.spa.appointments.form', compact('appointment', 'hotels', 'services', 'guests'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'spa_service_id' => 'required|exists:spa_services,id',
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_email' => 'nullable|email|max:255',
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'advance_paid' => 'nullable|numeric|min:0',
                'therapist_name' => 'nullable|string|max:255',
                'special_requests' => 'nullable|string',
                'internal_notes' => 'nullable|string',
                'is_room_charge' => 'nullable|in:yes,no',
            ]);

            $service = SpaService::find($request->spa_service_id);

            $price = $service->price;
            $discount = $request->discount_amount ?? 0;
            $taxable = $price - $discount;
            $tax = $request->tax_amount ?? 0;
            $total = $taxable + $tax;
            $advance = $request->advance_paid ?? 0;

            $apptNumber = 'SPA-' . Carbon::now()->format('Ymd') . '-' . str_pad(SpaAppointment::count() + 1, 4, '0', STR_PAD_LEFT);

            SpaAppointment::create([
                'hotel_id' => $request->hotel_id,
                'spa_service_id' => $request->spa_service_id,
                'guest_id' => $request->guest_id,
                'room_id' => $request->room_id,
                'appointment_number' => $apptNumber,
                'slug' => Helper::slug('spa_appointments', $apptNumber),
                'guest_name' => $request->guest_name,
                'guest_phone' => $request->guest_phone,
                'guest_email' => $request->guest_email,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'duration_minutes' => $service->duration_minutes,
                'price' => $price,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'advance_paid' => $advance,
                'therapist_name' => $request->therapist_name,
                'status' => 'pending',
                'payment_status' => $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid'),
                'special_requests' => $request->special_requests,
                'internal_notes' => $request->internal_notes,
                'created_by' => auth()->id(),
                'is_room_charge' => $request->input('is_room_charge', 'no'),
            ]);

            return redirect()->route('admin.spa.appointments.index')->with('success', 'Spa appointment booked successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(SpaAppointment $appointment)
    {
        $appointment->load(['hotel', 'spaService', 'guest', 'room', 'createdBy']);
        return view('admin.spa.appointments.show', compact('appointment'));
    }

    public function edit(SpaAppointment $appointment)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $services = SpaService::where('status', 'active')->get();
        $guests = Guest::where('status', 'active')->get();
        return view('admin.spa.appointments.form', compact('appointment', 'hotels', 'services', 'guests'));
    }

    public function update(Request $request, SpaAppointment $appointment)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'spa_service_id' => 'required|exists:spa_services,id',
                'guest_name' => 'required|string|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_email' => 'nullable|email|max:255',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'advance_paid' => 'nullable|numeric|min:0',
                'therapist_name' => 'nullable|string|max:255',
                'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
                'special_requests' => 'nullable|string',
                'internal_notes' => 'nullable|string',
                'is_room_charge' => 'nullable|in:yes,no',
            ]);

            $service = SpaService::find($request->spa_service_id);

            $price = $service->price;
            $discount = $request->discount_amount ?? 0;
            $taxable = $price - $discount;
            $tax = $request->tax_amount ?? 0;
            $total = $taxable + $tax;
            $advance = $request->advance_paid ?? 0;

            $appointment->update([
                'hotel_id' => $request->hotel_id,
                'spa_service_id' => $request->spa_service_id,
                'guest_id' => $request->guest_id,
                'room_id' => $request->room_id,
                'guest_name' => $request->guest_name,
                'guest_phone' => $request->guest_phone,
                'guest_email' => $request->guest_email,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'duration_minutes' => $service->duration_minutes,
                'price' => $price,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'advance_paid' => $advance,
                'therapist_name' => $request->therapist_name,
                'status' => $request->status,
                'payment_status' => $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid'),
                'special_requests' => $request->special_requests,
                'internal_notes' => $request->internal_notes,
                'is_room_charge' => $request->input('is_room_charge', 'no'),
            ]);

            return redirect()->route('admin.spa.appointments.index')->with('success', 'Spa appointment updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, SpaAppointment $appointment)
    {
        try {
            $appointment->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Spa appointment deleted successfully!']);
            }
            return redirect()->route('admin.spa.appointments.index')->with('success', 'Spa appointment deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, SpaAppointment $appointment)
    {
        try {
            $appointment->status = $appointment->status === 'confirmed' ? 'cancelled' : 'confirmed';
            $appointment->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $appointment->status, 'message' => 'Spa appointment status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Spa appointment status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
