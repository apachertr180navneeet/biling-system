<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tax;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class TaxController extends Controller
{
    public function index()
    {
        return view('admin.masters.taxes.index');
    }

    public function data(Request $request)
    {
        $query = Tax::query();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($tax) => route('admin.masters.taxes.status', $tax))
            ->addColumn('edit_url', fn($tax) => route('admin.masters.taxes.edit', $tax))
            ->addColumn('delete_url', fn($tax) => route('admin.masters.taxes.destroy', $tax))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $tax = null;
        return view('admin.masters.taxes.form', compact('tax'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:taxes',
                'rate' => 'required|numeric|min:0|max:100',
                'type' => 'required|in:percentage,fixed',
            ]);

            if ($request->is_default) {
                Tax::where('is_default', true)->update(['is_default' => false]);
            }

            $data = $request->all();
            $data['slug'] = Helper::slug('taxes', $request->name);

            Tax::create($data);

            return redirect()->route('admin.masters.taxes.index')->with('success', 'Tax created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Tax $tax)
    {
        return view('admin.masters.taxes.form', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:taxes,name,' . $tax->id,
                'rate' => 'required|numeric|min:0|max:100',
                'type' => 'required|in:percentage,fixed',
            ]);

            if ($request->is_default) {
                Tax::where('is_default', true)->where('id', '!=', $tax->id)->update(['is_default' => false]);
            }

            $tax->update($request->all());

            return redirect()->route('admin.masters.taxes.index')->with('success', 'Tax updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Tax $tax)
    {
        try {
            $tax->taxGroups()->detach();
            $tax->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Tax deleted successfully!']);
            }
            return redirect()->route('admin.masters.taxes.index')->with('success', 'Tax deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Tax $tax)
    {
        try {
            $tax->status = $tax->status === 'active' ? 'inactive' : 'active';
            $tax->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $tax->status, 'message' => 'Tax status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Tax status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
