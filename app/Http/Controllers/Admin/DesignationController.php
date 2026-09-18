<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Designation;
use App\Models\Department;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class DesignationController extends Controller
{
    public function index()
    {
        return view('admin.designations.index');
    }

    public function data(Request $request)
    {
        $query = Designation::with('department')->latest();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('department_name', fn($desig) => $desig->department?->name ?? '-')
            ->addColumn('status_url', fn($desig) => route('admin.designations.status', $desig))
            ->addColumn('edit_url', fn($desig) => route('admin.designations.edit', $desig))
            ->addColumn('delete_url', fn($desig) => route('admin.designations.destroy', $desig))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $designation = null;
        $departments = Department::where('status', 'active')->get();
        return view('admin.designations.form', compact('designation', 'departments'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'name' => 'required|string|max:255|unique:designations',
                'description' => 'nullable|string',
            ]);

            $data = $request->all();
            $data['slug'] = Helper::slug('designations', $request->name);

            Designation::create($data);

            return redirect()->route('admin.designations.index')->with('success', 'Designation created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Designation $designation)
    {
        $departments = Department::where('status', 'active')->get();
        return view('admin.designations.form', compact('designation', 'departments'));
    }

    public function update(Request $request, Designation $designation)
    {
        try {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'name' => 'required|string|max:255|unique:designations,name,' . $designation->id,
                'description' => 'nullable|string',
            ]);

            $designation->update($request->all());

            return redirect()->route('admin.designations.index')->with('success', 'Designation updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Designation $designation)
    {
        try {
            $designation->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Designation deleted successfully!']);
            }
            return redirect()->route('admin.designations.index')->with('success', 'Designation deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Designation $designation)
    {
        try {
            $designation->status = $designation->status === 'active' ? 'inactive' : 'active';
            $designation->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $designation->status, 'message' => 'Designation status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Designation status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
