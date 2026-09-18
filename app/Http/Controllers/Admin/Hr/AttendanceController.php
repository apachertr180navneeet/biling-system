<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('admin.hr.attendance.index');
    }

    public function data(Request $request)
    {
        $query = EmployeeAttendance::with('employee')->select('employee_attendances.*');
        return DataTables::of($query)
            ->filterColumn('date', function ($query, $value) {
                $query->where('date', 'like', "%{$value}%");
            })
            ->addColumn('employee_name', fn($att) => $att->employee?->first_name . ' ' . $att->employee?->last_name)
            ->addColumn('status_url', fn($att) => route('admin.hr.attendance.status', $att))
            ->addColumn('edit_url', fn($att) => route('admin.hr.attendance.edit', $att))
            ->addColumn('delete_url', fn($att) => route('admin.hr.attendance.destroy', $att))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $attendance = null;
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('admin.hr.attendance.form', compact('attendance', 'employees'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'status' => 'required|in:present,absent,half_day,late,leave,holiday',
                'check_in' => 'nullable|date_format:H:i',
                'check_out' => 'nullable|date_format:H:i',
                'hours_worked' => 'nullable|numeric|min:0|max:24',
                'overtime_hours' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            EmployeeAttendance::create($request->all());
            return redirect()->route('admin.hr.attendance.index')->with('success', 'Attendance recorded successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(EmployeeAttendance $attendance)
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('admin.hr.attendance.form', compact('attendance', 'employees'));
    }

    public function update(Request $request, EmployeeAttendance $attendance)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'status' => 'required|in:present,absent,half_day,late,leave,holiday',
                'check_in' => 'nullable|date_format:H:i',
                'check_out' => 'nullable|date_format:H:i',
                'hours_worked' => 'nullable|numeric|min:0|max:24',
                'overtime_hours' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $attendance->update($request->all());
            return redirect()->route('admin.hr.attendance.index')->with('success', 'Attendance updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, EmployeeAttendance $attendance)
    {
        try {
            $attendance->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Attendance deleted!']);
            }
            return redirect()->route('admin.hr.attendance.index')->with('success', 'Attendance deleted!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, EmployeeAttendance $attendance)
    {
        try {
            $attendance->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $attendance->status, 'message' => 'Status updated!']);
            }
            return redirect()->back()->with('success', 'Status updated!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
