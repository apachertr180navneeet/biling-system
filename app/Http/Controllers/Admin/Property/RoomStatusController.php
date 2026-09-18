<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomStatus;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class RoomStatusController extends Controller
{
    public function index()
    {
        return view('admin.property.room-status.index');
    }

    public function data(Request $request)
    {
        $query = RoomStatus::query();
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($roomStatus) => route('admin.property.room-status.status', $roomStatus))
            ->addColumn('edit_url', fn($roomStatus) => route('admin.property.room-status.edit', $roomStatus))
            ->addColumn('delete_url', fn($roomStatus) => route('admin.property.room-status.destroy', $roomStatus))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $roomStatus = null;
        return view('admin.property.room-status.form', compact('roomStatus'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'color' => 'required|string|max:255',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('room_statuses', $request->name);
            RoomStatus::create($data);
            return redirect()->route('admin.property.room-status.index')->with('success', 'Room Status created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RoomStatus $roomStatus)
    {
        return view('admin.property.room-status.form', compact('roomStatus'));
    }

    public function update(Request $request, RoomStatus $roomStatus)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'color' => 'required|string|max:255',
            ]);
            $roomStatus->update($request->all());
            return redirect()->route('admin.property.room-status.index')->with('success', 'Room Status updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, RoomStatus $roomStatus)
    {
        try {
            $roomStatus->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Room Status deleted successfully!']);
            }
            return redirect()->route('admin.property.room-status.index')->with('success', 'Room Status deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, RoomStatus $roomStatus)
    {
        try {
            $roomStatus->status = $roomStatus->status === 'active' ? 'inactive' : 'active';
            $roomStatus->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $roomStatus->status, 'message' => 'Room Status status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Room Status status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
