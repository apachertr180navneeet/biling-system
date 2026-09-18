<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\ServiceRequest;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use Carbon\Carbon;
use Exception;

class GuestAppController extends Controller
{
    public function showLogin()
    {
        if (session('guest_id')) {
            return redirect()->route('guest.dashboard');
        }
        return view('guest.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'reservation_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $reservation = Reservation::where('reservation_number', $request->reservation_number)
            ->whereHas('guest', function ($q) use ($request) {
                $q->where('email', $request->email);
            })
            ->with('guest', 'hotel', 'rooms.room')
            ->first();

        if (!$reservation) {
            return back()->withInput()->with('error', 'Invalid reservation number or email address.');
        }

        session(['guest_id' => $reservation->guest_id]);
        session(['guest_reservation_id' => $reservation->id]);

        return redirect()->route('guest.dashboard');
    }

    public function logout()
    {
        session()->forget(['guest_id', 'guest_reservation_id']);
        return redirect()->route('guest.login');
    }

    public function dashboard()
    {
        if (!session('guest_id')) {
            return redirect()->route('guest.login');
        }

        $guest = Guest::find(session('guest_id'));
        $reservation = Reservation::with('hotel', 'rooms.room', 'rooms.roomType')
            ->find(session('guest_reservation_id'));

        $serviceRequests = ServiceRequest::where('guest_id', session('guest_id'))
            ->where('reservation_id', session('guest_reservation_id'))
            ->latest()
            ->get();

        $stats = [
            'pending' => $serviceRequests->where('status', 'pending')->count(),
            'in_progress' => $serviceRequests->where('status', 'in_progress')->count(),
            'completed' => $serviceRequests->where('status', 'completed')->count(),
        ];

        return view('guest.dashboard', compact('guest', 'reservation', 'serviceRequests', 'stats'));
    }

    public function myBooking()
    {
        if (!session('guest_id')) {
            return redirect()->route('guest.login');
        }

        $reservation = Reservation::with('hotel', 'rooms.room', 'rooms.roomType', 'payments', 'checkIn')
            ->find(session('guest_reservation_id'));

        return view('guest.booking', compact('reservation'));
    }

    public function createServiceRequest()
    {
        if (!session('guest_id')) {
            return redirect()->route('guest.login');
        }

        $reservation = Reservation::with('hotel', 'rooms.room', 'rooms.roomType')
            ->find(session('guest_reservation_id'));

        $categories = [
            'housekeeping' => 'Housekeeping',
            'maintenance' => 'Maintenance',
            'room_service' => 'Room Service',
            'concierge' => 'Concierge',
            'amenity' => 'Amenity Request',
            'complaint' => 'Complaint',
            'other' => 'Other',
        ];

        return view('guest.service-request-form', compact('reservation', 'categories'));
    }

    public function storeServiceRequest(Request $request)
    {
        if (!session('guest_id')) {
            return redirect()->route('guest.login');
        }

        try {
            $request->validate([
                'category' => 'required|in:housekeeping,maintenance,room_service,concierge,amenity,complaint,other',
                'subject' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'room_id' => 'nullable|exists:rooms,id',
            ]);

            $reservation = Reservation::find(session('guest_reservation_id'));

            ServiceRequest::create([
                'request_number' => ServiceRequest::generateNumber(),
                'hotel_id' => $reservation->hotel_id,
                'reservation_id' => $reservation->id,
                'guest_id' => session('guest_id'),
                'room_id' => $request->room_id ?? $reservation->rooms->first()?->room_id,
                'category' => $request->category,
                'subject' => $request->subject,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => 'pending',
            ]);

            return redirect()->route('guest.dashboard')->with('success', 'Service request submitted successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function serviceRequestDetail(ServiceRequest $serviceRequest)
    {
        if (!session('guest_id')) {
            return redirect()->route('guest.login');
        }

        if ($serviceRequest->guest_id != session('guest_id')) {
            return redirect()->route('guest.dashboard')->with('error', 'Unauthorized access.');
        }

        $serviceRequest->load('room', 'assignedTo', 'reservation');

        return view('guest.service-request-detail', compact('serviceRequest'));
    }
}
