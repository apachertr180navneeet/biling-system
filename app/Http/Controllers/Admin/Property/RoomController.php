<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Wing;
use App\Models\RoomType;
use App\Models\BedType;
use App\Models\RoomStatus;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class RoomController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.property.rooms.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = Room::query()->with(['hotel', 'building', 'floor', 'wing', 'roomType', 'bedType', 'roomStatus']);
        return DataTables::of($query)
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('room_type_name', function ($query, $value) {
                $query->whereHas('roomType', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($room) => $room->hotel->name ?? '')
            ->addColumn('building_name', fn($room) => $room->building->name ?? '')
            ->addColumn('floor_name', fn($room) => $room->floor->name ?? '')
            ->addColumn('wing_name', fn($room) => $room->wing->name ?? '')
            ->addColumn('room_type_name', fn($room) => $room->roomType->name ?? '')
            ->addColumn('bed_type_name', fn($room) => $room->bedType->name ?? '')
            ->addColumn('room_status_name', fn($room) => $room->roomStatus->name ?? '')
            ->addColumn('room_status_color', fn($room) => $room->roomStatus->color ?? '#6c757d')
            ->addColumn('status_url', fn($room) => route('admin.property.rooms.status', $room))
            ->addColumn('edit_url', fn($room) => route('admin.property.rooms.edit', $room))
            ->addColumn('delete_url', fn($room) => route('admin.property.rooms.destroy', $room))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $room = null;
        $hotels = Hotel::where('status', 'active')->get();
        $buildings = Building::where('status', 'active')->get();
        $floors = Floor::where('status', 'active')->get();
        $wings = Wing::where('status', 'active')->get();
        $roomTypes = RoomType::where('status', 'active')->get();
        $bedTypes = BedType::where('status', 'active')->get();
        $roomStatuses = RoomStatus::where('status', 'active')->get();
        return view('admin.property.rooms.form', compact('room', 'hotels', 'buildings', 'floors', 'wings', 'roomTypes', 'bedTypes', 'roomStatuses'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required',
                'building_id' => 'required',
                'floor_id' => 'required',
                'wing_id' => 'required',
                'room_type_id' => 'required',
                'bed_type_id' => 'required',
                'room_number' => 'required|string|max:50',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('rooms', $request->room_number);
            Room::create($data);
            return redirect()->route('admin.property.rooms.index')->with('success', 'Room created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Room $room)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $buildings = Building::where('status', 'active')->get();
        $floors = Floor::where('status', 'active')->get();
        $wings = Wing::where('status', 'active')->get();
        $roomTypes = RoomType::where('status', 'active')->get();
        $bedTypes = BedType::where('status', 'active')->get();
        $roomStatuses = RoomStatus::where('status', 'active')->get();
        return view('admin.property.rooms.form', compact('room', 'hotels', 'buildings', 'floors', 'wings', 'roomTypes', 'bedTypes', 'roomStatuses'));
    }

    public function update(Request $request, Room $room)
    {
        try {
            $request->validate([
                'hotel_id' => 'required',
                'building_id' => 'required',
                'floor_id' => 'required',
                'wing_id' => 'required',
                'room_type_id' => 'required',
                'bed_type_id' => 'required',
                'room_number' => 'required|string|max:50',
            ]);
            $room->update($request->all());
            return redirect()->route('admin.property.rooms.index')->with('success', 'Room updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Room $room)
    {
        try {
            $room->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Room deleted successfully!']);
            }
            return redirect()->route('admin.property.rooms.index')->with('success', 'Room deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Room $room)
    {
        try {
            $room->status = $room->status === 'active' ? 'inactive' : 'active';
            $room->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $room->status, 'message' => 'Room status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Room status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
