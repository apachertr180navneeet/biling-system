<?php

namespace App\Http\Controllers\Admin\FrontOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CheckIn;
use App\Models\Reservation;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomStatus;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CheckInController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.front-office.check-ins.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = CheckIn::query()->with(['reservation', 'hotel', 'room', 'checkedInBy']);
        return DataTables::of($query)
            ->filterColumn('reservation_number', function ($query, $value) {
                $query->whereHas('reservation', function ($q) use ($value) {
                    $q->where('reservation_number', 'like', "%{$value}%");
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
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('reservation_number', fn($c) => $c->reservation->reservation_number ?? '')
            ->addColumn('guest_name', fn($c) => $c->reservation->guest->full_name ?? '')
            ->addColumn('hotel_name', fn($c) => $c->hotel->name ?? '')
            ->addColumn('room_number', fn($c) => $c->room->room_number ?? '')
            ->addColumn('check_in_date', fn($c) => $c->reservation->check_in_date?->format('d-m-Y') ?? '-')
            ->addColumn('arrival_time', fn($c) => $c->arrival_time?->format('d-m-Y H:i') ?? '')
            ->addColumn('status_url', fn($c) => route('admin.front-office.check-ins.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.front-office.check-ins.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.front-office.check-ins.destroy', $c))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $checkIn = null;
        $hotels = Hotel::where('status', 'active')->get();
        $reservations = Reservation::where('status', 'confirmed')->with(['guest', 'hotel'])->get();
        $rooms = Room::where('status', 'active')->get();
        return view('admin.front-office.check-ins.form', compact('checkIn', 'hotels', 'reservations', 'rooms'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'reservation_id' => 'required|exists:reservations,id',
                'hotel_id' => 'required',
                'room_id' => 'nullable|exists:rooms,id',
                'arrival_time' => 'nullable|date',
                'special_requests' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            $data = $request->all();
            $data['checked_in_by'] = auth()->id();
            $data['id_verified'] = $request->boolean('id_verified');

            $checkIn = CheckIn::create($data);

            $reservation = Reservation::findOrFail($request->reservation_id);
            $reservation->update([
                'status' => 'checked-in',
                'actual_check_in' => $request->arrival_time ?? now(),
            ]);

            if ($request->room_id) {
                $reservation->rooms()->updateOrCreate(
                    ['reservation_id' => $reservation->id],
                    ['room_id' => $request->room_id, 'status' => 'checked-in']
                );

                $occupiedStatus = RoomStatus::where('slug', 'occupied')->first();
                if ($occupiedStatus) {
                    Room::where('id', $request->room_id)->update(['room_status_id' => $occupiedStatus->id]);
                }
            }

            return redirect()->route('admin.front-office.check-ins.index')->with('success', 'Guest checked in successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(CheckIn $checkIn)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $reservations = Reservation::whereIn('status', ['confirmed', 'checked-in'])->with(['guest', 'hotel'])->get();
        $rooms = Room::where('status', 'active')->get();
        return view('admin.front-office.check-ins.form', compact('checkIn', 'hotels', 'reservations', 'rooms'));
    }

    public function update(Request $request, CheckIn $checkIn)
    {
        try {
            $request->validate([
                'reservation_id' => 'required|exists:reservations,id',
                'hotel_id' => 'required',
                'room_id' => 'nullable|exists:rooms,id',
                'arrival_time' => 'nullable|date',
                'special_requests' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            $data = $request->all();
            $data['id_verified'] = $request->boolean('id_verified');

            $checkIn->update($data);

            return redirect()->route('admin.front-office.check-ins.index')->with('success', 'Check-in updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, CheckIn $checkIn)
    {
        try {
            $reservation = $checkIn->reservation;
            if ($reservation && $reservation->status === 'checked-in') {
                $reservation->update(['status' => 'confirmed', 'actual_check_in' => null]);
            }

            if ($checkIn->room_id) {
                $vacantStatus = RoomStatus::where('slug', 'vacant')->first();
                if ($vacantStatus) {
                    Room::where('id', $checkIn->room_id)->update(['room_status_id' => $vacantStatus->id]);
                }
            }

            $checkIn->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Check-in deleted successfully!']);
            }
            return redirect()->route('admin.front-office.check-ins.index')->with('success', 'Check-in deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, CheckIn $checkIn)
    {
        try {
            $checkIn->status = $checkIn->status === 'active' ? 'inactive' : 'active';
            $checkIn->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $checkIn->status, 'message' => 'Check-in status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Check-in status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
