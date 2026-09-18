<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoyaltyTier;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyTransaction;
use App\Models\Guest;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class LoyaltyController extends Controller
{
    public function index()
    {
        return view('admin.crm.loyalty.index');
    }

    public function data(Request $request)
    {
        $query = LoyaltyMember::query()->with(['guest', 'loyaltyTier']);
        return DataTables::of($query)
            ->filterColumn('member_number', function ($query, $value) {
                $query->where('member_number', 'like', "%{$value}%");
            })
            ->filterColumn('guest_name', function ($query, $value) {
                $query->whereHas('guest', function ($q) use ($value) {
                    $q->where('first_name', 'like', "%{$value}%")
                        ->orWhere('last_name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('tier_name', function ($query, $value) {
                $query->whereHas('loyaltyTier', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('guest_name', fn($m) => $m->guest->full_name ?? '')
            ->addColumn('tier_name', fn($m) => $m->loyaltyTier->name ?? '')
            ->addColumn('tier_color', fn($m) => $m->loyaltyTier->color ?? '#6c757d')
            ->addColumn('total_spent_formatted', fn($m) => number_format($m->total_spent, 2))
            ->addColumn('status_url', fn($m) => route('admin.crm.loyalty.status', $m))
            ->addColumn('edit_url', fn($m) => route('admin.crm.loyalty.edit', $m))
            ->addColumn('delete_url', fn($m) => route('admin.crm.loyalty.destroy', $m))
            ->addColumn('transactions_url', fn($m) => route('admin.crm.loyalty.transactions', $m))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $member = null;
        $guests = Guest::where('status', 'active')->get();
        $tiers = LoyaltyTier::where('status', 'active')->get();
        return view('admin.crm.loyalty.form', compact('member', 'guests', 'tiers'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'guest_id' => 'required|exists:guests,id|unique:loyalty_members,guest_id',
                'loyalty_tier_id' => 'required|exists:loyalty_tiers,id',
            ]);

            $member = LoyaltyMember::create([
                'guest_id' => $request->guest_id,
                'loyalty_tier_id' => $request->loyalty_tier_id,
                'member_number' => LoyaltyMember::generateMemberNumber(),
                'enrolled_date' => now()->toDateString(),
                'status' => 'active',
            ]);

            return redirect()->route('admin.crm.loyalty.index')->with('success', 'Loyalty member enrolled successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(LoyaltyMember $member)
    {
        $guests = Guest::where('status', 'active')->get();
        $tiers = LoyaltyTier::where('status', 'active')->get();
        return view('admin.crm.loyalty.form', compact('member', 'guests', 'tiers'));
    }

    public function update(Request $request, LoyaltyMember $member)
    {
        try {
            $request->validate([
                'guest_id' => 'required|exists:guests,id|unique:loyalty_members,guest_id,' . $member->id,
                'loyalty_tier_id' => 'required|exists:loyalty_tiers,id',
            ]);

            $member->update($request->all());

            return redirect()->route('admin.crm.loyalty.index')->with('success', 'Loyalty member updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, LoyaltyMember $member)
    {
        try {
            $member->transactions()->delete();
            $member->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Loyalty member removed successfully!']);
            }
            return redirect()->route('admin.crm.loyalty.index')->with('success', 'Loyalty member removed successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, LoyaltyMember $member)
    {
        try {
            $member->status = $member->status === 'active' ? 'inactive' : 'active';
            $member->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $member->status, 'message' => 'Loyalty member status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Loyalty member status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function transactions(LoyaltyMember $member)
    {
        $transactions = LoyaltyTransaction::where('loyalty_member_id', $member->id)
            ->with('reservation')
            ->latest()
            ->get();
        return view('admin.crm.loyalty.transactions', compact('member', 'transactions'));
    }

    public function storeTransaction(Request $request, LoyaltyMember $member)
    {
        try {
            $request->validate([
                'type' => 'required|in:earned,redeemed,adjusted',
                'points' => 'required|integer|min:1',
                'description' => 'required|string|max:255',
                'reservation_id' => 'nullable|exists:reservations,id',
            ]);

            $points = $request->points;
            if ($request->type === 'redeemed' || $request->type === 'expired') {
                $points = -$points;
            }

            LoyaltyTransaction::create([
                'loyalty_member_id' => $member->id,
                'reservation_id' => $request->reservation_id,
                'type' => $request->type,
                'points' => $points,
                'description' => $request->description,
                'status' => 'active',
            ]);

            $member->update([
                'total_points' => $member->total_points + $points,
                'last_activity_date' => now()->toDateString(),
            ]);

            return redirect()->route('admin.crm.loyalty.transactions', $member)->with('success', 'Transaction recorded successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Loyalty Tiers CRUD
    public function tiers()
    {
        return view('admin.crm.loyalty.tiers-index');
    }

    public function tiersData(Request $request)
    {
        $query = LoyaltyTier::query();
        return DataTables::of($query)
            ->addColumn('min_points_formatted', fn($t) => number_format($t->min_points))
            ->addColumn('discount_badge', fn($t) => '<span class="badge bg-label-success">' . $t->discount_percentage . '%</span>')
            ->addColumn('multiplier_badge', fn($t) => '<span class="badge bg-label-info">' . $t->points_multiplier . 'x</span>')
            ->addColumn('color_badge', fn($t) => '<span class="badge" style="background-color:' . ($t->color ?? '#6c757d') . '">' . $t->name . '</span>')
            ->addColumn('status_url', fn($t) => route('admin.crm.loyalty.tiers.status', $t))
            ->addColumn('edit_url', fn($t) => route('admin.crm.loyalty.tiers.edit', $t))
            ->addColumn('delete_url', fn($t) => route('admin.crm.loyalty.tiers.destroy', $t))
            ->rawColumns(['discount_badge', 'multiplier_badge', 'color_badge'])
            ->make(true);
    }

    public function tierCreate()
    {
        $tier = null;
        return view('admin.crm.loyalty.tiers-form', compact('tier'));
    }

    public function tierStore(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:loyalty_tiers,name',
                'min_points' => 'required|integer|min:0',
                'discount_percentage' => 'required|numeric|min:0|max:100',
                'points_multiplier' => 'required|numeric|min:0.1|max:10',
                'description' => 'nullable|string',
                'color' => 'nullable|string|max:7',
            ]);

            $data = $request->all();
            $data['slug'] = \App\Helpers\Helper::slug('loyalty_tiers', $request->name);

            LoyaltyTier::create($data);

            return redirect()->route('admin.crm.loyalty.tiers.index')->with('success', 'Loyalty tier created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function tierEdit(LoyaltyTier $tier)
    {
        return view('admin.crm.loyalty.tiers-form', compact('tier'));
    }

    public function tierUpdate(Request $request, LoyaltyTier $tier)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:loyalty_tiers,name,' . $tier->id,
                'min_points' => 'required|integer|min:0',
                'discount_percentage' => 'required|numeric|min:0|max:100',
                'points_multiplier' => 'required|numeric|min:0.1|max:10',
                'description' => 'nullable|string',
                'color' => 'nullable|string|max:7',
            ]);

            $tier->update($request->all());

            return redirect()->route('admin.crm.loyalty.tiers.index')->with('success', 'Loyalty tier updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function tierDestroy(Request $request, LoyaltyTier $tier)
    {
        try {
            $tier->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Loyalty tier deleted successfully!']);
            }
            return redirect()->route('admin.crm.loyalty.tiers.index')->with('success', 'Loyalty tier deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function tierStatus(Request $request, LoyaltyTier $tier)
    {
        try {
            $tier->status = $tier->status === 'active' ? 'inactive' : 'active';
            $tier->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $tier->status, 'message' => 'Loyalty tier status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Loyalty tier status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
