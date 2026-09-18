<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialYear;
use App\Models\Company;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class FinancialYearController extends Controller
{
    public function index()
    {
        return view('admin.financial-years.index');
    }

    public function data(Request $request)
    {
        $query = FinancialYear::with('company')->latest();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('company_name', fn($fy) => $fy->company?->name ?? '-')
            ->addColumn('is_current', fn($fy) => $fy->is_current)
            ->addColumn('set_active_url', fn($fy) => route('admin.financial-years.set-active', $fy))
            ->addColumn('edit_url', fn($fy) => route('admin.financial-years.edit', $fy))
            ->addColumn('delete_url', fn($fy) => route('admin.financial-years.destroy', $fy))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $financialYear = null;
        $companies = Company::where('status', 'active')->get();
        return view('admin.financial-years.form', compact('financialYear', 'companies'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
                'name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            FinancialYear::create($request->all());

            return redirect()->route('admin.financial-years.index')->with('success', 'Financial year created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(FinancialYear $financialYear)
    {
        $companies = Company::where('status', 'active')->get();
        return view('admin.financial-years.form', compact('financialYear', 'companies'));
    }

    public function update(Request $request, FinancialYear $financialYear)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
                'name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            $financialYear->update($request->all());

            return redirect()->route('admin.financial-years.index')->with('success', 'Financial year updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, FinancialYear $financialYear)
    {
        try {
            $financialYear->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Financial year deleted successfully!']);
            }
            return redirect()->route('admin.financial-years.index')->with('success', 'Financial year deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function setActive(Request $request, FinancialYear $financialYear)
    {
        try {
            FinancialYear::where('company_id', $financialYear->company_id)->update(['is_current' => false]);
            $financialYear->update(['is_current' => true]);
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Financial year set as current successfully!']);
            }
            return redirect()->back()->with('success', 'Financial year set as current successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
