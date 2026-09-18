<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeZone;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class TimeZoneController extends Controller
{
    public function index()
    {
        return view('admin.timezones.index');
    }

    public function data(Request $request)
    {
        $query = TimeZone::query();

        return DataTables::of($query)
            ->addColumn('status_badge', function ($tz) {
                return $tz->status === 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($tz) {
                $editUrl = route('admin.timezones.edit', $tz);
                return "<a href='{$editUrl}' class='btn btn-sm btn-primary'><i class='bx bx-edit'></i></a>";
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $timezone = null;
        return view('admin.timezones.form', compact('timezone'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:timezone,name',
                'label' => 'nullable|string|max:255',
                'offset' => 'nullable|string|max:10',
                'offset_minutes' => 'nullable|integer',
            ]);

            $data = $request->all();
            $data['status'] = $request->boolean('status') ? 'active' : 'inactive';

            TimeZone::create($data);
            return redirect()->route('admin.timezones.index')->with('success', 'Timezone created!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(TimeZone $timezone)
    {
        return view('admin.timezones.form', compact('timezone'));
    }

    public function update(Request $request, TimeZone $timezone)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:timezone,name,' . $timezone->id,
                'label' => 'nullable|string|max:255',
                'offset' => 'nullable|string|max:10',
                'offset_minutes' => 'nullable|integer',
            ]);

            $data = $request->all();
            $data['status'] = $request->boolean('status') ? 'active' : 'inactive';

            $timezone->update($data);
            return redirect()->route('admin.timezones.index')->with('success', 'Timezone updated!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
