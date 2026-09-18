<?php

namespace App\Http\Controllers\Admin\TravelDesk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransportType;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class TransportTypeController extends Controller
{
    public function index()
    {
        return view('admin.travel-desk.types.index');
    }

    public function data(Request $request)
    {
        $query = TransportType::query();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($t) => route('admin.travel-desk.types.status', $t))
            ->addColumn('edit_url', fn($t) => route('admin.travel-desk.types.edit', $t))
            ->addColumn('delete_url', fn($t) => route('admin.travel-desk.types.destroy', $t))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $type = null;
        return view('admin.travel-desk.types.form', compact('type'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:transport_types,name',
                'description' => 'nullable|string',
                'base_price' => 'required|numeric|min:0',
                'per_km_rate' => 'nullable|numeric|min:0',
                'per_hour_rate' => 'nullable|numeric|min:0',
                'max_passengers' => 'required|numeric|min:1',
                'status' => 'required|in:active,inactive',
            ]);

            $data = $request->only(['name', 'description', 'base_price', 'per_km_rate', 'per_hour_rate', 'max_passengers', 'status']);
            $data['slug'] = Helper::slug('transport_types', $request->name);

            TransportType::create($data);

            return redirect()->route('admin.travel-desk.types.index')->with('success', 'Transport type created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(TransportType $type)
    {
        return view('admin.travel-desk.types.form', compact('type'));
    }

    public function update(Request $request, TransportType $type)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:transport_types,name,' . $type->id,
                'description' => 'nullable|string',
                'base_price' => 'required|numeric|min:0',
                'per_km_rate' => 'nullable|numeric|min:0',
                'per_hour_rate' => 'nullable|numeric|min:0',
                'max_passengers' => 'required|numeric|min:1',
                'status' => 'required|in:active,inactive',
            ]);

            $type->update($request->only(['name', 'description', 'base_price', 'per_km_rate', 'per_hour_rate', 'max_passengers', 'status']));

            return redirect()->route('admin.travel-desk.types.index')->with('success', 'Transport type updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, TransportType $type)
    {
        try {
            $type->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transport type deleted successfully!']);
            }
            return redirect()->route('admin.travel-desk.types.index')->with('success', 'Transport type deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, TransportType $type)
    {
        try {
            $type->status = $type->status === 'active' ? 'inactive' : 'active';
            $type->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $type->status, 'message' => 'Transport type status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Transport type status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
