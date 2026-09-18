<?php

namespace App\Http\Controllers\Admin\Housekeeping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CleaningSchedule;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CleaningScheduleController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.housekeeping.cleaning-schedules.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = CleaningSchedule::query()->with(['hotel', 'room', 'assignedTo']);
        return DataTables::of($query)
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('room_number', function ($query, $value) {
                $query->whereHas('room', function ($q) use ($value) {
                    $q->where('room_number', 'like', "%{$value}%");
                });
            })
            ->filterColumn('assigned_name', function ($query, $value) {
                $query->whereHas('assignedTo', function ($q) use ($value) {
                    $q->where('first_name', 'like', "%{$value}%")
                        ->orWhere('last_name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($c) => $c->hotel->name ?? '')
            ->addColumn('room_number', fn($c) => $c->room->room_number ?? '')
            ->addColumn('assigned_name', fn($c) => $c->assignedTo ? $c->assignedTo->first_name . ' ' . $c->assignedTo->last_name : 'Unassigned')
            ->addColumn('scheduled_date_formatted', fn($c) => $c->scheduled_date?->format('d-m-Y') ?? '')
            ->addColumn('priority_badge', function ($row) {
                $colors = ['high' => 'danger', 'medium' => 'warning', 'low' => 'info'];
                $color = $colors[$row->priority] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($row->priority) . '</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $colors = ['pending' => 'secondary', 'in_progress' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->status)) . '</span>';
            })
            ->addColumn('status_url', fn($c) => route('admin.housekeeping.cleaning-schedules.status', $c))
            ->addColumn('edit_url', fn($c) => route('admin.housekeeping.cleaning-schedules.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.housekeeping.cleaning-schedules.destroy', $c))
            ->rawColumns(['priority_badge', 'status_badge'])
            ->make(true);
    }

    public function create()
    {
        $cleaningSchedule = null;
        $hotels = Hotel::where('status', 'active')->get();
        $rooms = Room::where('status', 'active')->get();
        $users = User::where('status', 'active')->get();
        return view('admin.housekeeping.cleaning-schedules.form', compact('cleaningSchedule', 'hotels', 'rooms', 'users'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'room_id' => 'required|exists:rooms,id',
                'cleaning_type' => 'required|in:checkout,stay_over,deep_cleaning,turndown',
                'scheduled_date' => 'required|date',
                'scheduled_time' => 'nullable|date_format:H:i',
                'assigned_to' => 'nullable|exists:users,id',
                'priority' => 'required|in:high,medium,low',
                'notes' => 'nullable|string',
            ]);
            $data = $request->all();
            CleaningSchedule::create($data);
            return redirect()->route('admin.housekeeping.cleaning-schedules.index')->with('success', 'Cleaning Schedule created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(CleaningSchedule $cleaningSchedule)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $rooms = Room::where('status', 'active')->get();
        $users = User::where('status', 'active')->get();
        return view('admin.housekeeping.cleaning-schedules.form', compact('cleaningSchedule', 'hotels', 'rooms', 'users'));
    }

    public function update(Request $request, CleaningSchedule $cleaningSchedule)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'room_id' => 'required|exists:rooms,id',
                'cleaning_type' => 'required|in:checkout,stay_over,deep_cleaning,turndown',
                'scheduled_date' => 'required|date',
                'scheduled_time' => 'nullable|date_format:H:i',
                'assigned_to' => 'nullable|exists:users,id',
                'priority' => 'required|in:high,medium,low',
                'notes' => 'nullable|string',
            ]);
            $cleaningSchedule->update($request->all());
            return redirect()->route('admin.housekeeping.cleaning-schedules.index')->with('success', 'Cleaning Schedule updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, CleaningSchedule $cleaningSchedule)
    {
        try {
            $cleaningSchedule->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Cleaning Schedule deleted successfully!']);
            }
            return redirect()->route('admin.housekeeping.cleaning-schedules.index')->with('success', 'Cleaning Schedule deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, CleaningSchedule $cleaningSchedule)
    {
        try {
            $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
            $currentIndex = array_search($cleaningSchedule->status, $statuses);
            $nextIndex = ($currentIndex + 1) % count($statuses);
            $cleaningSchedule->status = $statuses[$nextIndex];

            if ($cleaningSchedule->status === 'completed') {
                $cleaningSchedule->completed_at = now();
                $cleaningSchedule->completed_by = auth()->id();
            }

            $cleaningSchedule->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $cleaningSchedule->status, 'message' => 'Status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
