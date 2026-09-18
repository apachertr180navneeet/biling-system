<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Floor;
use App\Models\Building;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class FloorController extends Controller
{
    public function index()
    {
        return view('admin.property.floors.index');
    }

    public function data(Request $request)
    {
        $query = Floor::query()->with('building');
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('building_name', function ($query, $value) {
                $query->whereHas('building', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('building_name', fn($floor) => $floor->building->name ?? '')
            ->addColumn('status_url', fn($floor) => route('admin.property.floors.status', $floor))
            ->addColumn('edit_url', fn($floor) => route('admin.property.floors.edit', $floor))
            ->addColumn('delete_url', fn($floor) => route('admin.property.floors.destroy', $floor))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $floor = null;
        $buildings = Building::with('hotel')->where('status', 'active')->get();
        return view('admin.property.floors.form', compact('floor', 'buildings'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'building_id' => 'required',
                'name' => 'required|string|max:255',
                'floor_number' => 'required|integer',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('floors', $request->name);
            Floor::create($data);
            return redirect()->route('admin.property.floors.index')->with('success', 'Floor created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Floor $floor)
    {
        $buildings = Building::with('hotel')->where('status', 'active')->get();
        return view('admin.property.floors.form', compact('floor', 'buildings'));
    }

    public function update(Request $request, Floor $floor)
    {
        try {
            $request->validate([
                'building_id' => 'required',
                'name' => 'required|string|max:255',
                'floor_number' => 'required|integer',
            ]);
            $floor->update($request->all());
            return redirect()->route('admin.property.floors.index')->with('success', 'Floor updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Floor $floor)
    {
        try {
            $floor->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Floor deleted successfully!']);
            }
            return redirect()->route('admin.property.floors.index')->with('success', 'Floor deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Floor $floor)
    {
        try {
            $floor->status = $floor->status === 'active' ? 'inactive' : 'active';
            $floor->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $floor->status, 'message' => 'Floor status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Floor status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
