<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CurrencyController extends Controller
{
    public function index()
    {
        return view('admin.masters.currencies.index');
    }

    public function data(Request $request)
    {
        $query = Currency::query();

        return DataTables::of($query)
            ->filterColumn('code', function ($query, $value) {
                $query->where('code', 'like', "%{$value}%");
            })
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($currency) => route('admin.masters.currencies.status', $currency))
            ->addColumn('edit_url', fn($currency) => route('admin.masters.currencies.edit', $currency))
            ->addColumn('delete_url', fn($currency) => route('admin.masters.currencies.destroy', $currency))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $currency = null;
        return view('admin.masters.currencies.form', compact('currency'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:10|unique:currencies',
                'name' => 'required|string|max:255',
                'symbol' => 'required|string|max:10',
                'exchange_rate' => 'required|numeric|min:0',
            ]);

            if ($request->is_default) {
                Currency::where('is_default', true)->update(['is_default' => false]);
            }

            Currency::create($request->all());

            return redirect()->route('admin.masters.currencies.index')->with('success', 'Currency created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Currency $currency)
    {
        return view('admin.masters.currencies.form', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id,
                'name' => 'required|string|max:255',
                'symbol' => 'required|string|max:10',
                'exchange_rate' => 'required|numeric|min:0',
            ]);

            if ($request->is_default) {
                Currency::where('is_default', true)->where('id', '!=', $currency->id)->update(['is_default' => false]);
            }

            $currency->update($request->all());

            return redirect()->route('admin.masters.currencies.index')->with('success', 'Currency updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Currency $currency)
    {
        try {
            $currency->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Currency deleted successfully!']);
            }
            return redirect()->route('admin.masters.currencies.index')->with('success', 'Currency deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Currency $currency)
    {
        try {
            $currency->status = $currency->status === 'active' ? 'inactive' : 'active';
            $currency->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $currency->status, 'message' => 'Currency status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Currency status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
