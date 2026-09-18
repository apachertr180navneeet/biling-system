<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class RoomTypeController extends Controller
{
    public function index()
    {
        return view('admin.property.room-types.index');
    }

    public function data(Request $request)
    {
        $query = RoomType::query();
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($roomType) => route('admin.property.room-types.status', $roomType))
            ->addColumn('edit_url', fn($roomType) => route('admin.property.room-types.edit', $roomType))
            ->addColumn('delete_url', fn($roomType) => route('admin.property.room-types.destroy', $roomType))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $roomType = null;
        return view('admin.property.room-types.form', compact('roomType'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'base_rate' => 'required|numeric',
                'max_occupancy' => 'required|integer',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('room_types', $request->name);
            RoomType::create($data);
            return redirect()->route('admin.property.room-types.index')->with('success', 'Room Type created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RoomType $roomType)
    {
        return view('admin.property.room-types.form', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'base_rate' => 'required|numeric',
                'max_occupancy' => 'required|integer',
            ]);
            $roomType->update($request->all());
            return redirect()->route('admin.property.room-types.index')->with('success', 'Room Type updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, RoomType $roomType)
    {
        try {
            $roomType->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Room Type deleted successfully!']);
            }
            return redirect()->route('admin.property.room-types.index')->with('success', 'Room Type deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, RoomType $roomType)
    {
        try {
            $roomType->status = $roomType->status === 'active' ? 'inactive' : 'active';
            $roomType->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $roomType->status, 'message' => 'Room Type status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Room Type status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
