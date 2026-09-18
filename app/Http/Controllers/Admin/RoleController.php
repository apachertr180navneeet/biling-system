<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index');
    }

    public function data(Request $request)
    {
        $query = Role::withCount('permissions')->latest();

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('slug', function ($query, $value) {
                $query->where('slug', 'like', "%{$value}%");
            })
            ->addColumn('edit_url', fn($role) => route('admin.roles.edit', $role))
            ->addColumn('delete_url', fn($role) => route('admin.roles.destroy', $role))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $grouped = $permissions->groupBy('module');
        return view('admin.roles.create', compact('grouped'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'description' => 'nullable|string',
                'permissions' => 'required|array|min:1',
            ]);

            $role = Role::create([
                'name' => $request->name,
                'slug' => Helper::slug('roles', $request->name),
                'description' => $request->description,
                'is_system' => false,
            ]);

            $role->permissions()->sync($request->permissions);

            return redirect()->route('admin.roles.index')->with('success', 'Role created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $grouped = $permissions->groupBy('module');
        $assignedPermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'grouped', 'assignedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'description' => 'nullable|string',
                'permissions' => 'required|array|min:1',
            ]);

            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $role->permissions()->sync($request->permissions);

            return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Role $role)
    {
        try {
            if ($role->is_system) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Cannot delete system roles.'], 403);
                }
                return back()->with('error', 'Cannot delete system roles.');
            }

            $role->permissions()->detach();
            $role->delete();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Role deleted successfully!']);
            }
            return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
