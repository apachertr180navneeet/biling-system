<?php

namespace App\Http\Controllers\Admin\Reservation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guest;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class GuestController extends Controller
{
    public function index()
    {
        return view('admin.reservation.guests.index');
    }

    public function data(Request $request)
    {
        $query = Guest::query();
        return DataTables::of($query)
            ->filterColumn('full_name', function ($query, $value) {
                $query->where('first_name', 'like', "%{$value}%")
                    ->orWhere('last_name', 'like', "%{$value}%");
            })
            ->addColumn('full_name', fn($guest) => $guest->full_name)
            ->addColumn('status_url', fn($guest) => route('admin.reservation.guests.status', $guest))
            ->addColumn('edit_url', fn($guest) => route('admin.reservation.guests.edit', $guest))
            ->addColumn('delete_url', fn($guest) => route('admin.reservation.guests.destroy', $guest))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $guest = null;
        return view('admin.reservation.guests.form', compact('guest'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('guests', $request->first_name . ' ' . ($request->last_name ?? ''));
            Guest::create($data);
            return redirect()->route('admin.reservation.guests.index')->with('success', 'Guest created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Guest $guest)
    {
        return view('admin.reservation.guests.form', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
            ]);
            $guest->update($request->all());
            return redirect()->route('admin.reservation.guests.index')->with('success', 'Guest updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Guest $guest)
    {
        try {
            $guest->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Guest deleted successfully!']);
            }
            return redirect()->route('admin.reservation.guests.index')->with('success', 'Guest deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function quickStore(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
            ]);
            $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'id_type', 'id_number', 'nationality', 'company_name']);
            $data['slug'] = Helper::slug('guests', $request->first_name . ' ' . ($request->last_name ?? ''));
            $data['status'] = 'active';
            $guest = Guest::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Guest created successfully!',
                'guest' => [
                    'id' => $guest->id,
                    'full_name' => $guest->full_name,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request, Guest $guest)
    {
        try {
            $guest->status = $guest->status === 'active' ? 'inactive' : 'active';
            $guest->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $guest->status, 'message' => 'Guest status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Guest status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
