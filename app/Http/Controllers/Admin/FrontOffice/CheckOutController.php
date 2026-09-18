<?php

namespace App\Http\Controllers\Admin\FrontOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CheckOut;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyTransaction;
use App\Models\LoyaltyTier;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CheckOutController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.front-office.check-outs.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = CheckOut::query()->with(['reservation', 'hotel', 'room', 'checkedOutBy']);
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
            ->addColumn('check_out_time', fn($c) => $c->check_out_time?->format('d-m-Y H:i') ?? '')
            ->addColumn('balance_due_formatted', fn($c) => number_format($c->balance_due, 2))
            ->addColumn('status_url', fn($c) => route('admin.front-office.check-outs.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.front-office.check-outs.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.front-office.check-outs.destroy', $c))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $checkOut = null;
        $hotels = Hotel::where('status', 'active')->get();
        $reservations = Reservation::where('status', 'checked-in')->with(['guest', 'hotel', 'rooms'])->get();
        $rooms = Room::where('status', 'active')->get();
        return view('admin.front-office.check-outs.form', compact('checkOut', 'hotels', 'reservations', 'rooms'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'reservation_id' => 'required|exists:reservations,id',
                'hotel_id' => 'required',
                'room_id' => 'nullable|exists:rooms,id',
                'final_bill_amount' => 'required|numeric|min:0',
                'total_charges' => 'required|numeric|min:0',
                'total_payments' => 'required|numeric|min:0',
                'damage_charges' => 'nullable|numeric|min:0',
                'feedback_rating' => 'nullable|integer|min:1|max:5',
            ]);

            $data = $request->all();
            $data['check_out_time'] = now();
            $data['balance_due'] = $data['final_bill_amount'] + ($data['damage_charges'] ?? 0) - $data['total_payments'];
            $data['checked_out_by'] = auth()->id();

            $checkOut = CheckOut::create($data);

            $reservation = Reservation::findOrFail($request->reservation_id);
            $reservation->update([
                'status' => 'checked-out',
                'actual_check_out' => now(),
                'paid_amount' => $data['total_payments'],
            ]);

            if ($request->room_id) {
                $dirtyStatus = RoomStatus::where('slug', 'dirty')->first();
                if ($dirtyStatus) {
                    Room::where('id', $request->room_id)->update(['room_status_id' => $dirtyStatus->id]);
                }
            }

            $this->earnLoyaltyPoints($reservation, $data['total_payments']);

            return redirect()->route('admin.front-office.check-outs.index')->with('success', 'Guest checked out successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(CheckOut $checkOut)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $reservations = Reservation::whereIn('status', ['checked-in', 'checked-out'])->with(['guest', 'hotel', 'rooms'])->get();
        $rooms = Room::where('status', 'active')->get();
        return view('admin.front-office.check-outs.form', compact('checkOut', 'hotels', 'reservations', 'rooms'));
    }

    public function update(Request $request, CheckOut $checkOut)
    {
        try {
            $request->validate([
                'reservation_id' => 'required|exists:reservations,id',
                'hotel_id' => 'required',
                'room_id' => 'nullable|exists:rooms,id',
                'final_bill_amount' => 'required|numeric|min:0',
                'total_charges' => 'required|numeric|min:0',
                'total_payments' => 'required|numeric|min:0',
                'damage_charges' => 'nullable|numeric|min:0',
                'feedback_rating' => 'nullable|integer|min:1|max:5',
            ]);

            $data = $request->all();
            $data['balance_due'] = $data['final_bill_amount'] + ($data['damage_charges'] ?? 0) - $data['total_payments'];

            $checkOut->update($data);

            return redirect()->route('admin.front-office.check-outs.index')->with('success', 'Check-out updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, CheckOut $checkOut)
    {
        try {
            $reservation = $checkOut->reservation;
            if ($reservation && $reservation->status === 'checked-out') {
                $reservation->update(['status' => 'checked-in', 'actual_check_out' => null]);
            }

            if ($checkOut->room_id) {
                $occupiedStatus = RoomStatus::where('slug', 'occupied')->first();
                if ($occupiedStatus) {
                    Room::where('id', $checkOut->room_id)->update(['room_status_id' => $occupiedStatus->id]);
                }
            }

            $checkOut->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Check-out deleted successfully!']);
            }
            return redirect()->route('admin.front-office.check-outs.index')->with('success', 'Check-out deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, CheckOut $checkOut)
    {
        try {
            $checkOut->status = $checkOut->status === 'active' ? 'inactive' : 'active';
            $checkOut->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $checkOut->status, 'message' => 'Check-out status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Check-out status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function invoice(CheckOut $checkOut)
    {
        $reservation = $checkOut->reservation->load(['guest', 'rooms.roomType', 'rooms.room', 'payments']);
        $hotel = $checkOut->hotel;

        $pdf = Pdf::loadView('admin.front-office.check-outs.invoice', compact('checkOut', 'reservation', 'hotel'));
        $pdf->setPaper('a4');
        return $pdf->download('invoice-' . $reservation->reservation_number . '.pdf');
    }

    private function earnLoyaltyPoints(Reservation $reservation, float $paymentAmount): void
    {
        if ($paymentAmount <= 0) {
            return;
        }

        $guest = $reservation->guest;
        if (!$guest) {
            return;
        }

        $member = LoyaltyMember::where('guest_id', $guest->id)->where('status', 'active')->first();
        if (!$member) {
            return;
        }

        $tier = $member->loyaltyTier;
        $multiplier = $tier->points_multiplier ?? 1.0;
        $points = (int) floor(($paymentAmount / 100) * $multiplier);

        if ($points <= 0) {
            return;
        }

        LoyaltyTransaction::create([
            'loyalty_member_id' => $member->id,
            'reservation_id' => $reservation->id,
            'type' => 'earned',
            'points' => $points,
            'description' => 'Points earned for reservation ' . $reservation->reservation_number,
            'reference_number' => 'LPN-' . strtoupper(uniqid()),
            'status' => 'active',
        ]);

        $member->update([
            'total_points' => $member->total_points + $points,
            'total_stays' => $member->total_stays + 1,
            'total_spent' => $member->total_spent + $paymentAmount,
            'last_activity_date' => now()->toDateString(),
        ]);

        $nextTier = LoyaltyTier::where('status', 'active')
            ->where('min_points', '<=', $member->total_points)
            ->orderBy('min_points', 'desc')
            ->first();

        if ($nextTier && $nextTier->id !== $member->loyalty_tier_id) {
            $member->update(['loyalty_tier_id' => $nextTier->id]);
        }
    }
}
