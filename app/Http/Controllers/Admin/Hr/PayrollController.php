<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\EmployeeAttendance;
use App\Helpers\Helper;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Exception;

class PayrollController extends Controller
{
    public function index()
    {
        return view('admin.hr.payroll.index');
    }

    public function data(Request $request)
    {
        $query = Payroll::select('payrolls.*');
        return DataTables::of($query)
            ->filterColumn('payroll_number', function ($query, $value) {
                $query->where('payroll_number', 'like', "%{$value}%");
            })
            ->filterColumn('period', function ($query, $value) {
                $query->where('period', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($pr) => route('admin.hr.payroll.status', $pr))
            ->addColumn('edit_url', fn($pr) => route('admin.hr.payroll.edit', $pr))
            ->addColumn('delete_url', fn($pr) => route('admin.hr.payroll.destroy', $pr))
            ->addColumn('show_url', fn($pr) => route('admin.hr.payroll.show', $pr))
            ->rawColumns([])
            ->make(true);
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('items.employee');
        return view('admin.hr.payroll.show', ['payroll' => $payroll]);
    }

    public function create()
    {
        $payroll = null;
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('admin.hr.payroll.form', compact('payroll', 'employees'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string|max:20',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'payment_date' => 'nullable|date',
                'employee_ids' => 'required|array|min:1',
            ]);

            DB::beginTransaction();

            $totalBasic = 0;
            $totalEarnings = 0;
            $totalDeductions = 0;
            $totalNetPay = 0;

            $payroll = Payroll::create([
                'hotel_id' => auth()->user()->branch_id,
                'payroll_number' => Helper::slug('payrolls', 'PAY-' . now()->format('YmdHis'), 'payroll_number'),
                'period' => $request->period,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'payment_date' => $request->payment_date,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->employee_ids as $empId) {
                $employee = Employee::find($empId);
                if (!$employee) continue;

                $daysInPeriod = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
                $attendance = EmployeeAttendance::where('employee_id', $empId)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->get();
                $daysPresent = $attendance->whereIn('status', ['present', 'late'])->count();
                $daysAbsent = $daysInPeriod - $daysPresent;

                $earnedBasic = $daysPresent > 0 ? ($employee->basic_salary / $daysInPeriod) * $daysPresent : 0;
                $earnedHra = $earnedBasic * 0.40;
                $earnedAllowances = $earnedBasic * 0.20;
                $earnings = $earnedBasic + $earnedHra + $earnedAllowances;

                $pfDeduction = $earnedBasic * 0.12;
                $esiDeduction = $earnings <= 21000 ? $earnings * 0.075 : 0;
                $deductions = $pfDeduction + $esiDeduction;
                $netPay = $earnings - $deductions;

                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $empId,
                    'basic_salary' => $employee->basic_salary,
                    'earnings' => $earnings,
                    'deductions' => $deductions,
                    'net_pay' => $netPay,
                    'days_present' => $daysPresent,
                    'days_absent' => $daysAbsent,
                    'earned_basic' => $earnedBasic,
                    'earned_hra' => $earnedHra,
                    'earned_allowances' => $earnedAllowances,
                    'pf_deduction' => $pfDeduction,
                    'esi_deduction' => $esiDeduction,
                    'status' => 'pending',
                ]);

                $totalBasic += $employee->basic_salary;
                $totalEarnings += $earnings;
                $totalDeductions += $deductions;
                $totalNetPay += $netPay;
            }

            $payroll->update([
                'total_basic' => $totalBasic,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'total_net_pay' => $totalNetPay,
                'total_employees' => $payroll->items()->count(),
            ]);

            DB::commit();
            return redirect()->route('admin.hr.payroll.index')->with('success', 'Payroll generated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('admin.hr.payroll.form', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        try {
            $request->validate([
                'period' => 'required|string|max:20',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'payment_date' => 'nullable|date',
            ]);

            $payroll->update($request->only(['period', 'start_date', 'end_date', 'payment_date']));
            return redirect()->route('admin.hr.payroll.index')->with('success', 'Payroll updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Payroll $payroll)
    {
        try {
            DB::beginTransaction();
            $payroll->items()->delete();
            $payroll->delete();
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Payroll deleted!']);
            }
            return redirect()->route('admin.hr.payroll.index')->with('success', 'Payroll deleted!');
        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Payroll $payroll)
    {
        try {
            DB::beginTransaction();

            $statuses = ['draft' => 'processing', 'processing' => 'approved', 'approved' => 'paid'];
            $newStatus = $statuses[$payroll->status] ?? $payroll->status;
            $payroll->status = $newStatus;
            $payroll->save();

            if ($newStatus === 'paid') {
                $payroll->items()->update(['status' => 'paid']);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $payroll->status, 'message' => 'Status updated!']);
            }
            return redirect()->back()->with('success', 'Status updated!');
        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
