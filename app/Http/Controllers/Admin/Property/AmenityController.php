<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amenity;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class AmenityController extends Controller
{
    public function index()
    {
        return view('admin.property.amenities.index');
    }

    public function data(Request $request)
    {
        $query = Amenity::query();
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($amenity) => route('admin.property.amenities.status', $amenity))
            ->addColumn('edit_url', fn($amenity) => route('admin.property.amenities.edit', $amenity))
            ->addColumn('delete_url', fn($amenity) => route('admin.property.amenities.destroy', $amenity))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $amenity = null;
        return view('admin.property.amenities.form', compact('amenity'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('amenities', $request->name);
            Amenity::create($data);
            return redirect()->route('admin.property.amenities.index')->with('success', 'Amenity created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Amenity $amenity)
    {
        return view('admin.property.amenities.form', compact('amenity'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $amenity->update($request->all());
            return redirect()->route('admin.property.amenities.index')->with('success', 'Amenity updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Amenity $amenity)
    {
        try {
            $amenity->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Amenity deleted successfully!']);
            }
            return redirect()->route('admin.property.amenities.index')->with('success', 'Amenity deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Amenity $amenity)
    {
        try {
            $amenity->status = $amenity->status === 'active' ? 'inactive' : 'active';
            $amenity->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $amenity->status, 'message' => 'Amenity status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Amenity status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
