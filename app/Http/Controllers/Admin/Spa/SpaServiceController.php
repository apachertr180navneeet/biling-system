<?php

namespace App\Http\Controllers\Admin\Spa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpaService;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class SpaServiceController extends Controller
{
    public function index()
    {
        return view('admin.spa.services.index');
    }

    public function data(Request $request)
    {
        $query = SpaService::query()->with(['hotel']);

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('category', function ($query, $value) {
                $query->where('category', 'like', "%{$value}%");
            })
            ->addColumn('hotel_name', fn($s) => $s->hotel->name ?? '')
            ->addColumn('status_url', fn($s) => route('admin.spa.services.status', $s))
            ->addColumn('edit_url', fn($s) => route('admin.spa.services.edit', $s))
            ->addColumn('delete_url', fn($s) => route('admin.spa.services.destroy', $s))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $service = null;
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.spa.services.form', compact('service', 'hotels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'duration_minutes' => 'required|numeric|min:1',
                'price' => 'required|numeric|min:0',
                'category' => 'nullable|string|max:255',
                'gender' => 'nullable|in:male,female,unisex',
                'status' => 'required|in:active,inactive',
            ]);

            $data = $request->only(['hotel_id', 'name', 'description', 'duration_minutes', 'price', 'category', 'gender', 'status']);
            $data['slug'] = Helper::slug('spa_services', $request->name);

            SpaService::create($data);

            return redirect()->route('admin.spa.services.index')->with('success', 'Spa service created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(SpaService $service)
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.spa.services.form', compact('service', 'hotels'));
    }

    public function update(Request $request, SpaService $service)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'duration_minutes' => 'required|numeric|min:1',
                'price' => 'required|numeric|min:0',
                'category' => 'nullable|string|max:255',
                'gender' => 'nullable|in:male,female,unisex',
                'status' => 'required|in:active,inactive',
            ]);

            $service->update($request->only(['hotel_id', 'name', 'description', 'duration_minutes', 'price', 'category', 'gender', 'status']));

            return redirect()->route('admin.spa.services.index')->with('success', 'Spa service updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, SpaService $service)
    {
        try {
            $service->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Spa service deleted successfully!']);
            }
            return redirect()->route('admin.spa.services.index')->with('success', 'Spa service deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, SpaService $service)
    {
        try {
            $service->status = $service->status === 'active' ? 'inactive' : 'active';
            $service->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $service->status, 'message' => 'Spa service status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Spa service status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
