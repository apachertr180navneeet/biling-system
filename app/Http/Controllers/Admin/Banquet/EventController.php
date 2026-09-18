<?php

namespace App\Http\Controllers\Admin\Banquet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Hall;
use App\Models\Hotel;
use App\Models\Guest;
use App\Models\EventService;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.banquet.events.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = Event::query()->with(['hall', 'hotel']);

        return DataTables::of($query)
            ->filterColumn('event_name', function ($query, $value) {
                $query->where('event_name', 'like', "%{$value}%");
            })
            ->filterColumn('event_number', function ($query, $value) {
                $query->where('event_number', 'like', "%{$value}%");
            })
            ->filterColumn('hall_name', function ($query, $value) {
                $query->whereHas('hall', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hall_name', fn($e) => $e->hall->name ?? '')
            ->addColumn('event_date_formatted', fn($e) => $e->event_date ? $e->event_date->format('d-m-Y') : '')
            ->addColumn('booking_status_badge', function ($row) {
                $colors = ['inquiry' => 'secondary', 'proposed' => 'info', 'confirmed' => 'primary', 'in_progress' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                $color = $colors[$row->booking_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->booking_status)) . '</span>';
            })
            ->addColumn('payment_status_badge', function ($row) {
                $colors = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success', 'refunded' => 'info'];
                $color = $colors[$row->payment_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->payment_status) . '</span>';
            })
            ->addColumn('status_url', fn($e) => route('admin.banquet.events.status', $e))
            ->addColumn('edit_url', fn($e) => route('admin.banquet.events.edit', $e))
            ->addColumn('delete_url', fn($e) => route('admin.banquet.events.destroy', $e))
            ->rawColumns(['booking_status_badge', 'payment_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $event = null;
        $hotels = Hotel::where('status', 'active')->get();
        $halls = Hall::where('status', 'active')->get();
        $guests = Guest::where('status', 'active')->get();
        $services = EventService::where('status', 'active')->get();
        return view('admin.banquet.events.form', compact('event', 'hotels', 'halls', 'guests', 'services'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'hall_id' => 'required|exists:halls,id',
                'event_name' => 'required|string|max:255',
                'event_type' => 'required|in:wedding,conference,seminar,exhibition,corporate,social,birthday,anniversary,other',
                'contact_name' => 'required|string|max:255',
                'contact_phone' => 'required|string|max:20',
                'contact_email' => 'nullable|email|max:255',
                'event_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'expected_guests' => 'required|numeric|min:1',
                'hall_charges' => 'required|numeric|min:0',
                'additional_charges' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'advance_paid' => 'nullable|numeric|min:0',
                'special_requests' => 'nullable|string',
                'internal_notes' => 'nullable|string',
                'booking_status' => 'required|in:inquiry,proposed,confirmed,in_progress,completed,cancelled',
                'services' => 'nullable|array',
                'services.*.id' => 'exists:event_services,id',
                'services.*.quantity' => 'numeric|min:1',
            ]);

            $hall = Hall::find($request->hall_id);
            $servicesCharges = 0;
            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    $service = EventService::find($svc['id']);
                    $servicesCharges += ($service->unit_price * ($svc['quantity'] ?? 1));
                }
            }

            $totalBeforeDiscount = $request->hall_charges + $servicesCharges + ($request->additional_charges ?? 0);
            $discount = $request->discount_amount ?? 0;
            $taxable = $totalBeforeDiscount - $discount;
            $tax = $request->tax_amount ?? 0;
            $total = $taxable + $tax;
            $advance = $request->advance_paid ?? 0;

            $eventNumber = 'EVT-' . Carbon::now()->format('Ymd') . '-' . str_pad(Event::count() + 1, 4, '0', STR_PAD_LEFT);

            $event = Event::create([
                'hotel_id' => $request->hotel_id,
                'hall_id' => $request->hall_id,
                'guest_id' => $request->guest_id,
                'event_number' => $eventNumber,
                'slug' => Helper::slug('events', $request->event_name),
                'event_name' => $request->event_name,
                'event_type' => $request->event_type,
                'contact_name' => $request->contact_name,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'event_date' => $request->event_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'expected_guests' => $request->expected_guests,
                'hall_charges' => $request->hall_charges,
                'services_charges' => $servicesCharges,
                'additional_charges' => $request->additional_charges ?? 0,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'advance_paid' => $advance,
                'balance_amount' => $total - $advance,
                'special_requests' => $request->special_requests,
                'internal_notes' => $request->internal_notes,
                'booking_status' => $request->booking_status,
                'payment_status' => $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid'),
                'created_by' => auth()->id(),
            ]);

            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    $service = EventService::find($svc['id']);
                    $qty = $svc['quantity'] ?? 1;
                    $event->services()->attach($svc['id'], [
                        'quantity' => $qty,
                        'unit_price' => $service->unit_price,
                        'total_price' => $service->unit_price * $qty,
                    ]);
                }
            }

            return redirect()->route('admin.banquet.events.index')->with('success', 'Event booked successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Event $event)
    {
        $event->load(['hall', 'hotel', 'guest', 'services', 'createdBy', 'approvedBy']);
        return view('admin.banquet.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $halls = Hall::where('status', 'active')->get();
        $guests = Guest::where('status', 'active')->get();
        $services = EventService::where('status', 'active')->get();
        $eventServiceIds = $event->services->pluck('id')->toArray();
        return view('admin.banquet.events.form', compact('event', 'hotels', 'halls', 'guests', 'services', 'eventServiceIds'));
    }

    public function update(Request $request, Event $event)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'hall_id' => 'required|exists:halls,id',
                'event_name' => 'required|string|max:255',
                'event_type' => 'required|in:wedding,conference,seminar,exhibition,corporate,social,birthday,anniversary,other',
                'contact_name' => 'required|string|max:255',
                'contact_phone' => 'required|string|max:20',
                'contact_email' => 'nullable|email|max:255',
                'event_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'expected_guests' => 'required|numeric|min:1',
                'hall_charges' => 'required|numeric|min:0',
                'additional_charges' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'advance_paid' => 'nullable|numeric|min:0',
                'special_requests' => 'nullable|string',
                'internal_notes' => 'nullable|string',
                'booking_status' => 'required|in:inquiry,proposed,confirmed,in_progress,completed,cancelled',
            ]);

            $servicesCharges = 0;
            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    $service = EventService::find($svc['id']);
                    $servicesCharges += ($service->unit_price * ($svc['quantity'] ?? 1));
                }
            }

            $totalBeforeDiscount = $request->hall_charges + $servicesCharges + ($request->additional_charges ?? 0);
            $discount = $request->discount_amount ?? 0;
            $taxable = $totalBeforeDiscount - $discount;
            $tax = $request->tax_amount ?? 0;
            $total = $taxable + $tax;
            $advance = $request->advance_paid ?? 0;

            $event->update([
                'hotel_id' => $request->hotel_id,
                'hall_id' => $request->hall_id,
                'guest_id' => $request->guest_id,
                'event_name' => $request->event_name,
                'event_type' => $request->event_type,
                'contact_name' => $request->contact_name,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'event_date' => $request->event_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'expected_guests' => $request->expected_guests,
                'hall_charges' => $request->hall_charges,
                'services_charges' => $servicesCharges,
                'additional_charges' => $request->additional_charges ?? 0,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'advance_paid' => $advance,
                'balance_amount' => $total - $advance,
                'special_requests' => $request->special_requests,
                'internal_notes' => $request->internal_notes,
                'booking_status' => $request->booking_status,
                'payment_status' => $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid'),
            ]);

            if ($request->has('services')) {
                $syncData = [];
                foreach ($request->services as $svc) {
                    $service = EventService::find($svc['id']);
                    $qty = $svc['quantity'] ?? 1;
                    $syncData[$svc['id']] = [
                        'quantity' => $qty,
                        'unit_price' => $service->unit_price,
                        'total_price' => $service->unit_price * $qty,
                    ];
                }
                $event->services()->sync($syncData);
            } else {
                $event->services()->detach();
            }

            return redirect()->route('admin.banquet.events.index')->with('success', 'Event updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Event $event)
    {
        try {
            $event->services()->detach();
            $event->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Event deleted successfully!']);
            }
            return redirect()->route('admin.banquet.events.index')->with('success', 'Event deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Event $event)
    {
        try {
            $event->booking_status = $event->booking_status === 'confirmed' ? 'cancelled' : 'confirmed';
            $event->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $event->booking_status, 'message' => 'Event status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Event status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
