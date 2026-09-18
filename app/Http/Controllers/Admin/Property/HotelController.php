<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Company;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class HotelController extends Controller
{
    public function index()
    {
        return view('admin.property.hotels.index');
    }

    public function data(Request $request)
    {
        $query = Hotel::query()->with('company');
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('company_name', function ($query, $value) {
                $query->whereHas('company', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('company_name', fn($hotel) => $hotel->company->name ?? '')
            ->addColumn('status_url', fn($hotel) => route('admin.property.hotels.status', $hotel))
            ->addColumn('edit_url', fn($hotel) => route('admin.property.hotels.edit', $hotel))
            ->addColumn('delete_url', fn($hotel) => route('admin.property.hotels.destroy', $hotel))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $hotel = null;
        $companies = Company::where('status', 'active')->get();
        return view('admin.property.hotels.form', compact('hotel', 'companies'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required',
                'name' => 'required|string|max:255',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('hotels', $request->name);
            Hotel::create($data);
            return redirect()->route('admin.property.hotels.index')->with('success', 'Hotel created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Hotel $hotel)
    {
        $companies = Company::where('status', 'active')->get();
        return view('admin.property.hotels.form', compact('hotel', 'companies'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        try {
            $request->validate([
                'company_id' => 'required',
                'name' => 'required|string|max:255',
            ]);
            $hotel->update($request->all());
            return redirect()->route('admin.property.hotels.index')->with('success', 'Hotel updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Hotel $hotel)
    {
        try {
            $hotel->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Hotel deleted successfully!']);
            }
            return redirect()->route('admin.property.hotels.index')->with('success', 'Hotel deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Hotel $hotel)
    {
        try {
            $hotel->status = $hotel->status === 'active' ? 'inactive' : 'active';
            $hotel->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $hotel->status, 'message' => 'Hotel status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Hotel status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
