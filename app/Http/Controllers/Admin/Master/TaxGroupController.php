<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxGroup;
use App\Models\Tax;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class TaxGroupController extends Controller
{
    public function index()
    {
        return view('admin.masters.tax-groups.index');
    }

    public function data(Request $request)
    {
        $query = TaxGroup::with('taxes')->latest();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('taxes_names', fn($tg) => $tg->taxes->pluck('name')->join(', ') ?: '-')
            ->addColumn('total_rate', fn($tg) => $tg->total_rate)
            ->addColumn('edit_url', fn($tg) => route('admin.masters.tax-groups.edit', $tg))
            ->addColumn('delete_url', fn($tg) => route('admin.masters.tax-groups.destroy', $tg))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $taxGroup = null;
        $taxes = Tax::where('status', 'active')->get();
        return view('admin.masters.tax-groups.form', compact('taxGroup', 'taxes'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:tax_groups',
                'description' => 'nullable|string',
                'tax_ids' => 'required|array|min:1',
            ]);

            $taxGroup = TaxGroup::create([
                'name' => $request->name,
                'slug' => Helper::slug('tax_groups', $request->name),
                'description' => $request->description,
            ]);

            foreach ($request->tax_ids as $index => $taxId) {
                $taxGroup->taxes()->attach($taxId, ['position' => $index]);
            }

            return redirect()->route('admin.masters.tax-groups.index')->with('success', 'Tax group created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(TaxGroup $taxGroup)
    {
        $taxGroup->load('taxes');
        $taxes = Tax::where('status', 'active')->get();
        $assignedTaxes = $taxGroup->taxes->pluck('id')->toArray();
        return view('admin.masters.tax-groups.form', compact('taxGroup', 'taxes', 'assignedTaxes'));
    }

    public function update(Request $request, TaxGroup $taxGroup)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:tax_groups,name,' . $taxGroup->id,
                'description' => 'nullable|string',
                'tax_ids' => 'required|array|min:1',
            ]);

            $taxGroup->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $taxGroup->taxes()->detach();

            foreach ($request->tax_ids as $index => $taxId) {
                $taxGroup->taxes()->attach($taxId, ['position' => $index]);
            }

            return redirect()->route('admin.masters.tax-groups.index')->with('success', 'Tax group updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, TaxGroup $taxGroup)
    {
        try {
            $taxGroup->taxes()->detach();
            $taxGroup->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Tax group deleted successfully!']);
            }
            return redirect()->route('admin.masters.tax-groups.index')->with('success', 'Tax group deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
