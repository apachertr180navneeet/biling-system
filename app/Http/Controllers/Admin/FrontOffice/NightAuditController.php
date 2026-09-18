<?php

namespace App\Http\Controllers\Admin\FrontOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NightAudit;
use App\Models\Hotel;
use App\Models\Reservation;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class NightAuditController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.front-office.night-audits.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = NightAudit::query()->with(['hotel', 'auditedBy']);
        return DataTables::of($query)
            ->filterColumn('audit_date', function ($query, $value) {
                $query->where('audit_date', $value);
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($n) => $n->hotel->name ?? '')
            ->addColumn('audit_date_formatted', fn($n) => $n->audit_date->format('d-m-Y'))
            ->addColumn('total_revenue_formatted', fn($n) => number_format($n->total_revenue, 2))
            ->addColumn('total_payments_formatted', fn($n) => number_format($n->total_payments_received, 2))
            ->addColumn('total_outstanding_formatted', fn($n) => number_format($n->total_outstanding, 2))
            ->addColumn('audited_by_name', fn($n) => $n->auditedBy->full_name ?? '')
            ->addColumn('status_url', fn($n) => route('admin.front-office.night-audits.status', $n))
            ->addColumn('edit_url', fn($n) => route('admin.front-office.night-audits.edit', $n))
            ->addColumn('delete_url', fn($n) => route('admin.front-office.night-audits.destroy', $n))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $nightAudit = null;
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.front-office.night-audits.form', compact('nightAudit', 'hotels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'audit_date' => 'required|date|unique:night_audits,audit_date',
                'hotel_id' => 'required|exists:hotels,id',
                'notes' => 'nullable|string',
            ]);

            $hotel = Hotel::findOrFail($request->hotel_id);
            $auditDate = $request->audit_date;

            $totalRooms = $hotel->rooms()->count();

            $occupiedRooms = Reservation::where('hotel_id', $hotel->id)
                ->where('status', 'checked-in')
                ->where('check_in_date', '<=', $auditDate)
                ->where('check_out_date', '>', $auditDate)
                ->count();

            $roomCharges = Reservation::where('hotel_id', $hotel->id)
                ->where('status', 'checked-in')
                ->where('check_in_date', '<=', $auditDate)
                ->where('check_out_date', '>', $auditDate)
                ->sum('total_amount');

            $taxCharges = round($roomCharges * 0.18, 2);

            $paymentsReceived = \App\Models\ReservationPayment::whereHas('reservation', function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })->whereDate('payment_date', $auditDate)->sum('amount');

            $noShowCount = Reservation::where('hotel_id', $hotel->id)
                ->where('status', 'confirmed')
                ->where('check_in_date', $auditDate)
                ->count();

            $cancellationCount = Reservation::where('hotel_id', $hotel->id)
                ->where('status', 'cancelled')
                ->whereDate('updated_at', $auditDate)
                ->count();

            $walkInCount = Reservation::where('hotel_id', $hotel->id)
                ->where('booking_source', 'walk-in')
                ->where('check_in_date', $auditDate)
                ->count();

            $nightAudit = NightAudit::create([
                'audit_date' => $auditDate,
                'hotel_id' => $hotel->id,
                'total_rooms_occupied' => $occupiedRooms,
                'total_rooms_available' => max(0, $totalRooms - $occupiedRooms),
                'room_charges_posted' => $roomCharges,
                'tax_charges_posted' => $taxCharges,
                'total_revenue' => $roomCharges + $taxCharges,
                'total_payments_received' => $paymentsReceived,
                'total_outstanding' => ($roomCharges + $taxCharges) - $paymentsReceived,
                'no_show_count' => $noShowCount,
                'cancellation_count' => $cancellationCount,
                'walk_in_count' => $walkInCount,
                'status' => 'completed',
                'notes' => $request->notes,
                'audited_by' => auth()->id(),
            ]);

            return redirect()->route('admin.front-office.night-audits.index')->with('success', 'Night audit completed successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(NightAudit $nightAudit)
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.front-office.night-audits.form', compact('nightAudit', 'hotels'));
    }

    public function update(Request $request, NightAudit $nightAudit)
    {
        try {
            $request->validate([
                'audit_date' => 'required|date|unique:night_audits,audit_date,' . $nightAudit->id . ',id',
                'hotel_id' => 'required|exists:hotels,id',
                'notes' => 'nullable|string',
            ]);

            $nightAudit->update([
                'hotel_id' => $request->hotel_id,
                'audit_date' => $request->audit_date,
                'notes' => $request->notes,
            ]);

            return redirect()->route('admin.front-office.night-audits.index')->with('success', 'Night audit updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, NightAudit $nightAudit)
    {
        try {
            $nightAudit->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Night audit deleted successfully!']);
            }
            return redirect()->route('admin.front-office.night-audits.index')->with('success', 'Night audit deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, NightAudit $nightAudit)
    {
        try {
            $nightAudit->status = $nightAudit->status === 'pending' ? 'completed' : 'pending';
            $nightAudit->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $nightAudit->status, 'message' => 'Night audit status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Night audit status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
