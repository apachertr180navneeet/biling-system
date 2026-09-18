<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GuestProfile;
use App\Models\Guest;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class GuestProfileController extends Controller
{
    public function index()
    {
        return view('admin.crm.guest-profiles.index');
    }

    public function data(Request $request)
    {
        $query = GuestProfile::query()->with(['guest']);
        return DataTables::of($query)
            ->filterColumn('guest_name', function ($query, $value) {
                $query->whereHas('guest', function ($q) use ($value) {
                    $q->where('first_name', 'like', "%{$value}%")
                        ->orWhere('last_name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('id_number', function ($query, $value) {
                $query->where('id_number', 'like', "%{$value}%");
            })
            ->addColumn('guest_name', fn($p) => $p->guest->full_name ?? '')
            ->addColumn('guest_email', fn($p) => $p->guest->email ?? '')
            ->addColumn('guest_phone', fn($p) => $p->guest->phone ?? '')
            ->addColumn('vip_badge', function ($p) {
                if (!$p->vip_status) return '';
                $colors = ['gold' => 'warning', 'platinum' => 'info', 'diamond' => 'primary'];
                $color = $colors[$p->vip_level] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . ucfirst($p->vip_level) . '</span>';
            })
            ->addColumn('status_url', fn($p) => route('admin.crm.guest-profiles.status', $p))
            ->addColumn('edit_url', fn($p) => route('admin.crm.guest-profiles.edit', $p))
            ->addColumn('delete_url', fn($p) => route('admin.crm.guest-profiles.destroy', $p))
            ->rawColumns(['vip_badge'])
            ->make(true);
    }

    public function create()
    {
        $guestProfile = null;
        $guests = Guest::where('status', 'active')->get();
        return view('admin.crm.guest-profiles.form', compact('guestProfile', 'guests'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'guest_id' => 'required|exists:guests,id|unique:guest_profiles,guest_id',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'id_type' => 'nullable|string|max:50',
                'id_number' => 'nullable|string|max:100',
                'id_expiry_date' => 'nullable|date',
                'occupation' => 'nullable|string|max:100',
                'dietary_preference' => 'nullable|string|max:50',
                'room_preference' => 'nullable|string|max:100',
                'bed_preference' => 'nullable|string|max:50',
                'pillow_preference' => 'nullable|string|max:50',
                'arrival_preference' => 'nullable|string|max:50',
                'communication_preference' => 'nullable|string|max:50',
                'special_notes' => 'nullable|string',
                'vip_status' => 'nullable|boolean',
                'vip_level' => 'nullable|string|max:50',
            ]);

            $data = $request->all();
            $data['id_verified'] = $request->boolean('id_verified');

            GuestProfile::create($data);

            return redirect()->route('admin.crm.guest-profiles.index')->with('success', 'Guest profile created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(GuestProfile $guestProfile)
    {
        $guests = Guest::where('status', 'active')->get();
        return view('admin.crm.guest-profiles.form', compact('guestProfile', 'guests'));
    }

    public function update(Request $request, GuestProfile $guestProfile)
    {
        try {
            $request->validate([
                'guest_id' => 'required|exists:guests,id|unique:guest_profiles,guest_id,' . $guestProfile->id,
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'id_type' => 'nullable|string|max:50',
                'id_number' => 'nullable|string|max:100',
                'id_expiry_date' => 'nullable|date',
                'occupation' => 'nullable|string|max:100',
                'dietary_preference' => 'nullable|string|max:50',
                'room_preference' => 'nullable|string|max:100',
                'bed_preference' => 'nullable|string|max:50',
                'pillow_preference' => 'nullable|string|max:50',
                'arrival_preference' => 'nullable|string|max:50',
                'communication_preference' => 'nullable|string|max:50',
                'special_notes' => 'nullable|string',
                'vip_status' => 'nullable|boolean',
                'vip_level' => 'nullable|string|max:50',
            ]);

            $data = $request->all();
            $data['id_verified'] = $request->boolean('id_verified');

            $guestProfile->update($data);

            return redirect()->route('admin.crm.guest-profiles.index')->with('success', 'Guest profile updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, GuestProfile $guestProfile)
    {
        try {
            $guestProfile->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Guest profile deleted successfully!']);
            }
            return redirect()->route('admin.crm.guest-profiles.index')->with('success', 'Guest profile deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, GuestProfile $guestProfile)
    {
        try {
            $guestProfile->status = $guestProfile->status === 'active' ? 'inactive' : 'active';
            $guestProfile->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $guestProfile->status, 'message' => 'Guest profile status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Guest profile status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
