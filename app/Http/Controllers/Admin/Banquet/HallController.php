<?php

namespace App\Http\Controllers\Admin\Banquet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hall;
use App\Models\HallAmenity;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class HallController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.banquet.halls.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = Hall::query()->with(['hotel']);

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($h) => $h->hotel->name ?? '')
            ->addColumn('amenities_list', function ($hall) {
                return $hall->amenities->pluck('name')->implode(', ');
            })
            ->addColumn('status_url', fn($h) => route('admin.banquet.halls.status', $h))
            ->addColumn('edit_url', fn($h) => route('admin.banquet.halls.edit', $h))
            ->addColumn('delete_url', fn($h) => route('admin.banquet.halls.destroy', $h))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $hall = null;
        $hotels = Hotel::where('status', 'active')->get();
        $amenities = HallAmenity::where('status', 'active')->get();
        return view('admin.banquet.halls.form', compact('hall', 'hotels', 'amenities'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'capacity' => 'required|numeric|min:1',
                'area_sqft' => 'nullable|numeric|min:0',
                'base_price' => 'required|numeric|min:0',
                'price_unit' => 'required|string|max:50',
                'floor' => 'nullable|string|max:50',
                'is_ac' => 'nullable|boolean',
                'has_projector' => 'nullable|boolean',
                'has_stage' => 'nullable|boolean',
                'has_sound_system' => 'nullable|boolean',
                'has_parking' => 'nullable|boolean',
                'amenities' => 'nullable|array',
                'amenities.*' => 'exists:hall_amenities,id',
                'status' => 'required|in:active,inactive,maintenance',
            ]);
            $data = $request->except(['amenities']);
            $data['slug'] = Helper::slug('halls', $request->name);
            $data['is_ac'] = $request->boolean('is_ac');
            $data['has_projector'] = $request->boolean('has_projector');
            $data['has_stage'] = $request->boolean('has_stage');
            $data['has_sound_system'] = $request->boolean('has_sound_system');
            $data['has_parking'] = $request->boolean('has_parking');
            $hall = Hall::create($data);
            if ($request->has('amenities')) {
                foreach ($request->amenities as $amenityId) {
                    $hall->amenities()->attach($amenityId, ['additional_cost' => 0]);
                }
            }
            return redirect()->route('admin.banquet.halls.index')->with('success', 'Hall created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Hall $hall)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $amenities = HallAmenity::where('status', 'active')->get();
        $hallAmenityIds = $hall->amenities->pluck('id')->toArray();
        return view('admin.banquet.halls.form', compact('hall', 'hotels', 'amenities', 'hallAmenityIds'));
    }

    public function update(Request $request, Hall $hall)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'capacity' => 'required|numeric|min:1',
                'area_sqft' => 'nullable|numeric|min:0',
                'base_price' => 'required|numeric|min:0',
                'price_unit' => 'required|string|max:50',
                'floor' => 'nullable|string|max:50',
                'is_ac' => 'nullable|boolean',
                'has_projector' => 'nullable|boolean',
                'has_stage' => 'nullable|boolean',
                'has_sound_system' => 'nullable|boolean',
                'has_parking' => 'nullable|boolean',
                'amenities' => 'nullable|array',
                'amenities.*' => 'exists:hall_amenities,id',
                'status' => 'required|in:active,inactive,maintenance',
            ]);
            $data = $request->except(['amenities']);
            $data['is_ac'] = $request->boolean('is_ac');
            $data['has_projector'] = $request->boolean('has_projector');
            $data['has_stage'] = $request->boolean('has_stage');
            $data['has_sound_system'] = $request->boolean('has_sound_system');
            $data['has_parking'] = $request->boolean('has_parking');
            $hall->update($data);
            $hall->amenities()->sync($request->amenities ?? []);
            return redirect()->route('admin.banquet.halls.index')->with('success', 'Hall updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Hall $hall)
    {
        try {
            $hall->amenities()->detach();
            $hall->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Hall deleted successfully!']);
            }
            return redirect()->route('admin.banquet.halls.index')->with('success', 'Hall deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Hall $hall)
    {
        try {
            $hall->status = $hall->status === 'active' ? 'inactive' : 'active';
            $hall->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $hall->status, 'message' => 'Hall status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Hall status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
