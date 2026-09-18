<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Company;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class BranchController extends Controller
{
    public function index()
    {
        return view('admin.branches.index');
    }

    public function data(Request $request)
    {
        $query = Branch::with('company')->latest();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('email', function ($query, $value) {
                $query->where('email', 'like', "%{$value}%");
            })
            ->filterColumn('phone', function ($query, $value) {
                $query->where('phone', 'like', "%{$value}%");
            })
            ->addColumn('company_name', fn($branch) => $branch->company?->name ?? '-')
            ->addColumn('is_head_office', fn($branch) => $branch->is_head_office)
            ->addColumn('status_url', fn($branch) => route('admin.branches.status', $branch))
            ->addColumn('edit_url', fn($branch) => route('admin.branches.edit', $branch))
            ->addColumn('delete_url', fn($branch) => route('admin.branches.destroy', $branch))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $branch = null;
        $companies = Company::where('status', 'active')->get();
        return view('admin.branches.form', compact('branch', 'companies'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'zipcode' => 'nullable|string',
                'is_head_office' => 'boolean',
            ]);

            $data = $request->all();
            $data['slug'] = Helper::slug('branches', $request->name);

            Branch::create($data);

            return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Branch $branch)
    {
        $companies = Company::where('status', 'active')->get();
        return view('admin.branches.form', compact('branch', 'companies'));
    }

    public function update(Request $request, Branch $branch)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'zipcode' => 'nullable|string',
                'is_head_office' => 'boolean',
            ]);

            $data = $request->all();
            $branch->update($data);

            return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Branch $branch)
    {
        try {
            $branch->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Branch deleted successfully!']);
            }
            return redirect()->route('admin.branches.index')->with('success', 'Branch deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Branch $branch)
    {
        try {
            $branch->status = $branch->status === 'active' ? 'inactive' : 'active';
            $branch->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $branch->status, 'message' => 'Branch status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Branch status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
