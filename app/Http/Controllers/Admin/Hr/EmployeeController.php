<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Branch;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('admin.hr.employees.index');
    }

    public function data(Request $request)
    {
        $query = Employee::with('department', 'designation')->select('employees.*');
        return DataTables::of($query)
            ->filterColumn('first_name', function ($query, $value) {
                $query->where('first_name', 'like', "%{$value}%");
            })
            ->filterColumn('employee_id', function ($query, $value) {
                $query->where('employee_id', 'like', "%{$value}%");
            })
            ->addColumn('full_name', fn($emp) => $emp->first_name . ' ' . $emp->last_name)
            ->addColumn('status_url', fn($emp) => route('admin.hr.employees.status', $emp))
            ->addColumn('edit_url', fn($emp) => route('admin.hr.employees.edit', $emp))
            ->addColumn('delete_url', fn($emp) => route('admin.hr.employees.destroy', $emp))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $employee = null;
        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        return view('admin.hr.employees.form', compact('employee', 'departments', 'designations', 'branches'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:employees,email',
                'phone' => 'nullable|string|max:20',
                'gender' => 'nullable|in:male,female,other',
                'date_of_birth' => 'nullable|date',
                'department_id' => 'nullable|exists:departments,id',
                'designation_id' => 'nullable|exists:designations,id',
                'branch_id' => 'nullable|exists:branches,id',
                'date_of_joining' => 'required|date',
                'basic_salary' => 'required|numeric|min:0',
                'employment_type' => 'required|in:full_time,part_time,contract,intern',
                'status' => 'required|in:active,inactive',
            ]);

            $data = $request->all();
            $data['employee_id'] = Helper::slug('employees', 'EMP-' . now()->format('YmdHis'), 'employee_id');
            $data['slug'] = Helper::slug('employees', $request->first_name . ' ' . $request->last_name);
            Employee::create($data);

            return redirect()->route('admin.hr.employees.index')->with('success', 'Employee created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Employee $employee)
    {
        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        return view('admin.hr.employees.form', compact('employee', 'departments', 'designations', 'branches'));
    }

    public function update(Request $request, Employee $employee)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:employees,email,' . $employee->id,
                'phone' => 'nullable|string|max:20',
                'gender' => 'nullable|in:male,female,other',
                'date_of_birth' => 'nullable|date',
                'department_id' => 'nullable|exists:departments,id',
                'designation_id' => 'nullable|exists:designations,id',
                'branch_id' => 'nullable|exists:branches,id',
                'date_of_joining' => 'required|date',
                'basic_salary' => 'required|numeric|min:0',
                'employment_type' => 'required|in:full_time,part_time,contract,intern',
                'status' => 'required|in:active,inactive',
            ]);

            $employee->update($request->all());
            return redirect()->route('admin.hr.employees.index')->with('success', 'Employee updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Employee $employee)
    {
        try {
            $employee->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Employee deleted!']);
            }
            return redirect()->route('admin.hr.employees.index')->with('success', 'Employee deleted!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Employee $employee)
    {
        try {
            $employee->status = $employee->status === 'active' ? 'inactive' : 'active';
            $employee->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $employee->status, 'message' => 'Status updated!']);
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
