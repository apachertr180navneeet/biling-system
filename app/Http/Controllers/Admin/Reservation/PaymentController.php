<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use Exception;

class PaymentController extends Controller
{
    public function index(Reservation $reservation)
    {
        $reservation->load(['payments', 'guest', 'hotel', 'rooms.room', 'rooms.roomType']);
        return view('admin.reservation.payments.index', compact('reservation'));
    }

    public function store(Request $request, Reservation $reservation)
    {
        try {
            $request->validate([
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:cash,card,bank_transfer,online,other',
            ]);

            $payment = ReservationPayment::create([
                'reservation_id' => $reservation->id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            $reservation->paid_amount = $reservation->payments()->where('status', 'completed')->sum('amount');
            $reservation->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Payment recorded successfully!']);
            }
            return redirect()->route('admin.reservation.payments.index', $reservation)->with('success', 'Payment recorded successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Reservation $reservation, ReservationPayment $payment)
    {
        try {
            $payment->delete();
            $reservation->paid_amount = $reservation->payments()->where('status', 'completed')->sum('amount');
            $reservation->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Payment deleted successfully!']);
            }
            return redirect()->route('admin.reservation.payments.index', $reservation)->with('success', 'Payment deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
