<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RatePlan;
use App\Models\Hotel;
use App\Models\RoomType;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class RatePlanController extends Controller
{
    public function index()
    {
        return view('admin.reservation.rate-plans.index');
    }

    public function data(Request $request)
    {
        $query = RatePlan::query()->with(['hotel', 'roomType']);
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($rp) => $rp->hotel->name ?? '')
            ->addColumn('room_type_name', fn($rp) => $rp->roomType->name ?? '')
            ->addColumn('effective_from_formatted', fn($rp) => $rp->effective_from->format('d-m-Y'))
            ->addColumn('effective_to_formatted', fn($rp) => $rp->effective_to->format('d-m-Y'))
            ->addColumn('status_url', fn($rp) => route('admin.reservation.rate-plans.status', $rp))
            ->addColumn('edit_url', fn($rp) => route('admin.reservation.rate-plans.edit', $rp))
            ->addColumn('delete_url', fn($rp) => route('admin.reservation.rate-plans.destroy', $rp))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $ratePlan = null;
        $hotels = Hotel::where('status', 'active')->get();
        $roomTypes = RoomType::where('status', 'active')->get();
        return view('admin.reservation.rate-plans.form', compact('ratePlan', 'hotels', 'roomTypes'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required',
                'room_type_id' => 'required',
                'name' => 'required|string|max:255',
                'rate_per_night' => 'required|numeric|min:0',
                'effective_from' => 'required|date',
                'effective_to' => 'required|date|after_or_equal:effective_from',
            ]);
            RatePlan::create($request->all());
            return redirect()->route('admin.reservation.rate-plans.index')->with('success', 'Rate plan created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RatePlan $ratePlan)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $roomTypes = RoomType::where('status', 'active')->get();
        return view('admin.reservation.rate-plans.form', compact('ratePlan', 'hotels', 'roomTypes'));
    }

    public function update(Request $request, RatePlan $ratePlan)
    {
        try {
            $request->validate([
                'hotel_id' => 'required',
                'room_type_id' => 'required',
                'name' => 'required|string|max:255',
                'rate_per_night' => 'required|numeric|min:0',
                'effective_from' => 'required|date',
                'effective_to' => 'required|date|after_or_equal:effective_from',
            ]);
            $ratePlan->update($request->all());
            return redirect()->route('admin.reservation.rate-plans.index')->with('success', 'Rate plan updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, RatePlan $ratePlan)
    {
        try {
            $ratePlan->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Rate plan deleted successfully!']);
            }
            return redirect()->route('admin.reservation.rate-plans.index')->with('success', 'Rate plan deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, RatePlan $ratePlan)
    {
        try {
            $ratePlan->status = $ratePlan->status === 'active' ? 'inactive' : 'active';
            $ratePlan->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $ratePlan->status, 'message' => 'Rate plan status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Rate plan status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
