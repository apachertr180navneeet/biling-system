<?php

namespace App\Http\Controllers\Admin\ChannelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomTypeChannelMapping;
use App\Models\OtaChannel;
use App\Models\RoomType;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class ChannelMappingController extends Controller
{
    public function index()
    {
        return view('admin.channel-manager.mappings.index');
    }

    public function data(Request $request)
    {
        $query = RoomTypeChannelMapping::with(['otaChannel', 'roomType'])->latest();

        return DataTables::of($query)
            ->filterColumn('ota_channel_name', function ($query, $value) {
                $query->whereHas('otaChannel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('ota_channel_name', fn($mapping) => $mapping->otaChannel?->name ?? '-')
            ->addColumn('room_type_name', fn($mapping) => $mapping->roomType?->name ?? '-')
            ->addColumn('edit_url', fn($mapping) => route('admin.channel-manager.mappings.edit', $mapping))
            ->addColumn('delete_url', fn($mapping) => route('admin.channel-manager.mappings.destroy', $mapping))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $mapping = null;
        $channels = OtaChannel::where('status', 'active')->get();
        $roomTypes = RoomType::all();
        return view('admin.channel-manager.mappings.form', compact('mapping', 'channels', 'roomTypes'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ota_channel_id' => 'required|exists:ota_channels,id',
                'room_type_id' => 'required|exists:room_types,id',
                'ota_room_type_id' => 'nullable|string',
                'ota_room_name' => 'nullable|string',
                'rate_multiplier' => 'required|numeric|min:0.01|max:9.99',
            ]);

            RoomTypeChannelMapping::create($request->only([
                'ota_channel_id', 'room_type_id', 'ota_room_type_id', 'ota_room_name',
                'rate_multiplier', 'sync_rates', 'sync_availability',
            ]));

            return redirect()->route('admin.channel-manager.mappings.index')->with('success', 'Room mapping created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RoomTypeChannelMapping $mapping)
    {
        $channels = OtaChannel::where('status', 'active')->get();
        $roomTypes = RoomType::all();
        return view('admin.channel-manager.mappings.form', compact('mapping', 'channels', 'roomTypes'));
    }

    public function update(Request $request, RoomTypeChannelMapping $mapping)
    {
        try {
            $request->validate([
                'ota_channel_id' => 'required|exists:ota_channels,id',
                'room_type_id' => 'required|exists:room_types,id',
                'ota_room_type_id' => 'nullable|string',
                'ota_room_name' => 'nullable|string',
                'rate_multiplier' => 'required|numeric|min:0.01|max:9.99',
            ]);

            $mapping->update($request->only([
                'ota_channel_id', 'room_type_id', 'ota_room_type_id', 'ota_room_name',
                'rate_multiplier', 'sync_rates', 'sync_availability', 'status',
            ]));

            return redirect()->route('admin.channel-manager.mappings.index')->with('success', 'Room mapping updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, RoomTypeChannelMapping $mapping)
    {
        try {
            $mapping->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Room mapping deleted successfully!']);
            }
            return redirect()->route('admin.channel-manager.mappings.index')->with('success', 'Room mapping deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
