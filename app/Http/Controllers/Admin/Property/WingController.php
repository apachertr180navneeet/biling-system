<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wing;
use App\Models\Floor;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class WingController extends Controller
{
    public function index()
    {
        return view('admin.property.wings.index');
    }

    public function data(Request $request)
    {
        $query = Wing::query()->with('floor');
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('floor_name', function ($query, $value) {
                $query->whereHas('floor', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('floor_name', fn($wing) => $wing->floor->name ?? '')
            ->addColumn('status_url', fn($wing) => route('admin.property.wings.status', $wing))
            ->addColumn('edit_url', fn($wing) => route('admin.property.wings.edit', $wing))
            ->addColumn('delete_url', fn($wing) => route('admin.property.wings.destroy', $wing))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $wing = null;
        $floors = Floor::with('building')->where('status', 'active')->get();
        return view('admin.property.wings.form', compact('wing', 'floors'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'floor_id' => 'required',
                'name' => 'required|string|max:255',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('wings', $request->name);
            Wing::create($data);
            return redirect()->route('admin.property.wings.index')->with('success', 'Wing created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Wing $wing)
    {
        $floors = Floor::with('building')->where('status', 'active')->get();
        return view('admin.property.wings.form', compact('wing', 'floors'));
    }

    public function update(Request $request, Wing $wing)
    {
        try {
            $request->validate([
                'floor_id' => 'required',
                'name' => 'required|string|max:255',
            ]);
            $wing->update($request->all());
            return redirect()->route('admin.property.wings.index')->with('success', 'Wing updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Wing $wing)
    {
        try {
            $wing->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Wing deleted successfully!']);
            }
            return redirect()->route('admin.property.wings.index')->with('success', 'Wing deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Wing $wing)
    {
        try {
            $wing->status = $wing->status === 'active' ? 'inactive' : 'active';
            $wing->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $wing->status, 'message' => 'Wing status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Wing status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
