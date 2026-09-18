<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Building;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class BuildingController extends Controller
{
    public function index()
    {
        return view('admin.property.buildings.index');
    }

    public function data(Request $request)
    {
        $query = Building::query()->with('hotel');
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($building) => $building->hotel->name ?? '')
            ->addColumn('status_url', fn($building) => route('admin.property.buildings.status', $building))
            ->addColumn('edit_url', fn($building) => route('admin.property.buildings.edit', $building))
            ->addColumn('delete_url', fn($building) => route('admin.property.buildings.destroy', $building))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $building = null;
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.property.buildings.form', compact('building', 'hotels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required',
                'name' => 'required|string|max:255',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('buildings', $request->name);
            Building::create($data);
            return redirect()->route('admin.property.buildings.index')->with('success', 'Building created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Building $building)
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.property.buildings.form', compact('building', 'hotels'));
    }

    public function update(Request $request, Building $building)
    {
        try {
            $request->validate([
                'hotel_id' => 'required',
                'name' => 'required|string|max:255',
            ]);
            $building->update($request->all());
            return redirect()->route('admin.property.buildings.index')->with('success', 'Building updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Building $building)
    {
        try {
            $building->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Building deleted successfully!']);
            }
            return redirect()->route('admin.property.buildings.index')->with('success', 'Building deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Building $building)
    {
        try {
            $building->status = $building->status === 'active' ? 'inactive' : 'active';
            $building->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $building->status, 'message' => 'Building status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Building status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
