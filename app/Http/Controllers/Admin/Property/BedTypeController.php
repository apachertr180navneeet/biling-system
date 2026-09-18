<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BedType;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class BedTypeController extends Controller
{
    public function index()
    {
        return view('admin.property.bed-types.index');
    }

    public function data(Request $request)
    {
        $query = BedType::query();
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($bedType) => route('admin.property.bed-types.status', $bedType))
            ->addColumn('edit_url', fn($bedType) => route('admin.property.bed-types.edit', $bedType))
            ->addColumn('delete_url', fn($bedType) => route('admin.property.bed-types.destroy', $bedType))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $bedType = null;
        return view('admin.property.bed-types.form', compact('bedType'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('bed_types', $request->name);
            BedType::create($data);
            return redirect()->route('admin.property.bed-types.index')->with('success', 'Bed Type created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(BedType $bedType)
    {
        return view('admin.property.bed-types.form', compact('bedType'));
    }

    public function update(Request $request, BedType $bedType)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $bedType->update($request->all());
            return redirect()->route('admin.property.bed-types.index')->with('success', 'Bed Type updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, BedType $bedType)
    {
        try {
            $bedType->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Bed Type deleted successfully!']);
            }
            return redirect()->route('admin.property.bed-types.index')->with('success', 'Bed Type deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, BedType $bedType)
    {
        try {
            $bedType->status = $bedType->status === 'active' ? 'inactive' : 'active';
            $bedType->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $bedType->status, 'message' => 'Bed Type status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Bed Type status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
