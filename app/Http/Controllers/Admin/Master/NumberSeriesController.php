<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NumberSeries;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class NumberSeriesController extends Controller
{
    public function index()
    {
        return view('admin.masters.number-series.index');
    }

    public function data(Request $request)
    {
        $query = NumberSeries::query();

        return DataTables::of($query)
            ->filterColumn('module', function ($query, $value) {
                $query->where('module', 'like', "%{$value}%");
            })
            ->addColumn('preview', fn($ns) => $ns->peekNextNumber())
            ->addColumn('edit_url', fn($ns) => route('admin.masters.number-series.edit', $ns))
            ->addColumn('delete_url', fn($ns) => route('admin.masters.number-series.destroy', $ns))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $numberSeries = null;
        return view('admin.masters.number-series.form', compact('numberSeries'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'module' => 'required|string|max:255|unique:number_series',
                'prefix' => 'nullable|string|max:20',
                'suffix' => 'nullable|string|max:20',
                'next_number' => 'required|integer|min:1',
                'padding' => 'required|integer|min:1|max:10',
                'description' => 'nullable|string',
            ]);

            NumberSeries::create($request->all());

            return redirect()->route('admin.masters.number-series.index')->with('success', 'Number series created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(NumberSeries $numberSeries)
    {
        return view('admin.masters.number-series.form', compact('numberSeries'));
    }

    public function update(Request $request, NumberSeries $numberSeries)
    {
        try {
            $request->validate([
                'module' => 'required|string|max:255|unique:number_series,module,' . $numberSeries->id,
                'prefix' => 'nullable|string|max:20',
                'suffix' => 'nullable|string|max:20',
                'next_number' => 'required|integer|min:1',
                'padding' => 'required|integer|min:1|max:10',
                'description' => 'nullable|string',
            ]);

            $numberSeries->update($request->all());

            return redirect()->route('admin.masters.number-series.index')->with('success', 'Number series updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, NumberSeries $numberSeries)
    {
        try {
            $numberSeries->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Number series deleted successfully!']);
            }
            return redirect()->route('admin.masters.number-series.index')->with('success', 'Number series deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
