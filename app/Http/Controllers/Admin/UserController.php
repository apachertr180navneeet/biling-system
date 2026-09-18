<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function data(Request $request)
    {
        $query = User::with('roleDetail')->latest();

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        return DataTables::of($query)
            ->filterColumn('full_name', function ($query, $value) {
                $query->where('full_name', 'like', "%{$value}%");
            })
            ->filterColumn('email', function ($query, $value) {
                $query->where('email', 'like', "%{$value}%");
            })
            ->filterColumn('phone', function ($query, $value) {
                $query->where('phone', 'like', "%{$value}%");
            })
            ->addColumn('role_name', fn($user) => $user->roleDetail?->name ?? $user->role)
            ->addColumn('is_super_admin', fn($user) => $user->isSuperAdmin())
            ->addColumn('status_url', fn($user) => route('admin.users.status', $user))
            ->addColumn('edit_url', fn($user) => route('admin.users.edit', $user))
            ->addColumn('delete_url', fn($user) => route('admin.users.destroy', $user))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $roles = Role::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();
        return view('admin.users.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'required|min:9|unique:users',
                'password' => 'required|min:6|confirmed',
                'role_id' => 'required|exists:roles,id',
                'branch_id' => 'nullable|exists:branches,id',
            ]);

            $data = $request->all();
            $data['slug'] = Helper::slug('users', $request->first_name . ' ' . $request->last_name);
            $data['full_name'] = $request->first_name . ' ' . $request->last_name;
            $data['password'] = Hash::make($request->password);
            $data['country'] = $request->country ?? '';

            unset($data['avatar']);
            if ($request->file('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . $file->getClientOriginalName();
                $folder = 'uploads/user/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $file->move($path, $filename);
                $data['avatar'] = $folder . $filename;
            }

            User::create($data);

            return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();
        return view('admin.users.edit', compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|min:9|unique:users,phone,' . $user->id,
                'role_id' => 'required|exists:roles,id',
                'branch_id' => 'nullable|exists:branches,id',
            ]);

            $data = $request->all();
            $data['full_name'] = $request->first_name . ' ' . $request->last_name;
            $data['country'] = $request->country ?? '';

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }

            unset($data['avatar']);
            if ($request->file('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . $file->getClientOriginalName();
                $folder = 'uploads/user/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $file->move($path, $filename);
                $data['avatar'] = $folder . $filename;
            }

            $user->update($data);

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, User $user)
    {
        try {
            $user->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
            }
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, User $user)
    {
        try {
            if ($user->isSuperAdmin()) {
                throw new Exception('Super admin status cannot be changed.');
            }
            $user->status = $user->status === 'active' ? 'inactive' : 'active';
            $user->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $user->status, 'message' => 'User status updated successfully!']);
            }
            return redirect()->back()->with('success', 'User status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
