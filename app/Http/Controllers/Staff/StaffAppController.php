<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\CleaningSchedule;
use App\Models\MaintenanceWorkOrder;
use App\Models\Room;
use App\Models\Hotel;
use Carbon\Carbon;
use Exception;

class StaffAppController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $hotelId = $user->branch_id ?? Hotel::first()?->id;

        $myTasks = ServiceRequest::where('hotel_id', $hotelId)
            ->where('assigned_to', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('room', 'guest')
            ->latest()
            ->get();

        $unassignedTasks = ServiceRequest::where('hotel_id', $hotelId)
            ->whereNull('assigned_to')
            ->where('status', 'pending')
            ->with('room', 'guest')
            ->latest()
            ->get();

        $todayStats = [
            'my_assigned' => $myTasks->count(),
            'unassigned' => $unassignedTasks->count(),
            'completed_today' => ServiceRequest::where('hotel_id', $hotelId)
                ->where('assigned_to', $user->id)
                ->whereDate('completed_at', Carbon::today())
                ->count(),
        ];

        return view('staff.dashboard', compact('myTasks', 'unassignedTasks', 'todayStats'));
    }

    public function myTasks()
    {
        $user = auth()->user();
        $hotelId = $user->branch_id ?? Hotel::first()?->id;

        $tasks = ServiceRequest::where('hotel_id', $hotelId)
            ->where('assigned_to', $user->id)
            ->with('room', 'guest')
            ->latest()
            ->get();

        return view('staff.my-tasks', compact('tasks'));
    }

    public function unassignedTasks()
    {
        $user = auth()->user();
        $hotelId = $user->branch_id ?? Hotel::first()?->id;

        $tasks = ServiceRequest::where('hotel_id', $hotelId)
            ->whereNull('assigned_to')
            ->where('status', 'pending')
            ->with('room', 'guest')
            ->latest()
            ->get();

        return view('staff.unassigned-tasks', compact('tasks'));
    }

    public function claimTask(ServiceRequest $task)
    {
        try {
            $user = auth()->user();

            if ($task->assigned_to) {
                return back()->with('error', 'Task already assigned to another staff member.');
            }

            $task->update([
                'assigned_to' => $user->id,
                'assigned_at' => Carbon::now(),
                'status' => 'assigned',
            ]);

            return redirect()->route('staff.my-tasks')->with('success', 'Task claimed successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, ServiceRequest $task)
    {
        try {
            $request->validate([
                'status' => 'required|in:in_progress,completed',
                'notes' => 'nullable|string',
                'resolution_notes' => 'nullable|string',
            ]);

            $user = auth()->user();

            if ($task->assigned_to != $user->id) {
                return back()->with('error', 'You can only update tasks assigned to you.');
            }

            $updateData = [
                'status' => $request->status,
                'notes' => $request->notes,
            ];

            if ($request->status === 'in_progress') {
                $updateData['started_at'] = Carbon::now();
            } elseif ($request->status === 'completed') {
                $updateData['completed_at'] = Carbon::now();
                $updateData['resolution_notes'] = $request->resolution_notes;
            }

            $task->update($updateData);

            return back()->with('success', 'Task status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function housekeeping()
    {
        $user = auth()->user();
        $hotelId = $user->branch_id ?? Hotel::first()?->id;

        $rooms = Room::where('hotel_id', $hotelId)
            ->with(['roomType', 'roomStatus'])
            ->get();

        $housekeepingTasks = ServiceRequest::where('hotel_id', $hotelId)
            ->where('category', 'housekeeping')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('room', 'guest')
            ->latest()
            ->get();

        return view('staff.housekeeping', compact('rooms', 'housekeepingTasks'));
    }

    public function maintenance()
    {
        $user = auth()->user();
        $hotelId = $user->branch_id ?? Hotel::first()?->id;

        $maintenanceTasks = ServiceRequest::where('hotel_id', $hotelId)
            ->where('category', 'maintenance')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('room', 'guest')
            ->latest()
            ->get();

        $workOrders = MaintenanceWorkOrder::where('hotel_id', $hotelId)
            ->whereNotIn('status', ['completed'])
            ->with('asset')
            ->latest()
            ->get();

        return view('staff.maintenance', compact('maintenanceTasks', 'workOrders'));
    }

    public function taskDetail(ServiceRequest $task)
    {
        $task->load('room', 'guest', 'assignedTo', 'reservation');
        return view('staff.task-detail', compact('task'));
    }
}
