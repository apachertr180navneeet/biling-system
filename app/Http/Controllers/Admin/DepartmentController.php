<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Company;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('admin.departments.index');
    }

    public function data(Request $request)
    {
        $query = Department::with('company')->latest();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('company_name', fn($dept) => $dept->company?->name ?? '-')
            ->addColumn('status_url', fn($dept) => route('admin.departments.status', $dept))
            ->addColumn('edit_url', fn($dept) => route('admin.departments.edit', $dept))
            ->addColumn('delete_url', fn($dept) => route('admin.departments.destroy', $dept))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $department = null;
        $companies = Company::where('status', 'active')->get();
        return view('admin.departments.form', compact('department', 'companies'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
                'name' => 'required|string|max:255|unique:departments',
                'description' => 'nullable|string',
            ]);

            $data = $request->all();
            $data['slug'] = Helper::slug('departments', $request->name);

            Department::create($data);

            return redirect()->route('admin.departments.index')->with('success', 'Department created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Department $department)
    {
        $companies = Company::where('status', 'active')->get();
        return view('admin.departments.form', compact('department', 'companies'));
    }

    public function update(Request $request, Department $department)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
                'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
                'description' => 'nullable|string',
            ]);

            $department->update($request->all());

            return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Department $department)
    {
        try {
            $department->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Department deleted successfully!']);
            }
            return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Department $department)
    {
        try {
            $department->status = $department->status === 'active' ? 'inactive' : 'active';
            $department->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $department->status, 'message' => 'Department status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Department status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
