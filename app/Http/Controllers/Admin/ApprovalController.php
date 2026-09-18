<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovalRecord;
use App\Models\ApprovalRule;
use App\Services\ApprovalService;
use Yajra\DataTables\Facades\DataTables;

class ApprovalController extends Controller
{
    public function index()
    {
        return view('admin.approvals.index');
    }

    public function data(Request $request)
    {
        $query = ApprovalRecord::with(['requester', 'approver', 'approvalRule']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('requester_name', fn($r) => $r->requester?->name ?? '-')
            ->addColumn('approver_name', fn($r) => $r->approver?->name ?? '-')
            ->addColumn('module_badge', function ($r) {
                return '<span class="badge bg-info">' . ucfirst(str_replace('_', ' ', $r->module)) . '</span>';
            })
            ->addColumn('status_badge', function ($r) {
                $colors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'secondary'];
                $color = $colors[$r->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($r->status) . '</span>';
            })
            ->addColumn('formatted_date', fn($r) => $r->created_at?->format('d M Y, h:i A') ?? '')
            ->addColumn('actions', function ($r) {
                if ($r->status !== 'pending') return '';
                $approveUrl = route('admin.approvals.approve', $r);
                $rejectUrl = route('admin.approvals.reject', $r);
                return "
                    <div class='btn-group btn-group-sm'>
                        <button class='btn btn-success btn-approve' data-url='{$approveUrl}' title='Approve'><i class='bx bx-check'></i></button>
                        <button class='btn btn-danger btn-reject' data-url='{$rejectUrl}' title='Reject'><i class='bx bx-x'></i></button>
                    </div>";
            })
            ->rawColumns(['module_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    public function approve(Request $request, ApprovalRecord $approval)
    {
        try {
            ApprovalService::approve($approval, $request->remarks);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Approved successfully!']);
            }
            return redirect()->back()->with('success', 'Request approved!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, ApprovalRecord $approval)
    {
        try {
            ApprovalService::reject($approval, $request->remarks);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Rejected!']);
            }
            return redirect()->back()->with('success', 'Request rejected!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function rules()
    {
        $rules = ApprovalRule::with(['role', 'approver'])->get();
        return view('admin.approvals.rules', compact('rules'));
    }

    public function storeRule(Request $request)
    {
        try {
            $request->validate([
                'module' => 'required|string',
                'name' => 'required|string',
                'min_amount' => 'nullable|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
                'role_id' => 'nullable|exists:roles,id',
                'approver_id' => 'nullable|exists:users,id',
            ]);

            ApprovalRule::create($request->all());
            return redirect()->back()->with('success', 'Approval rule created!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroyRule(ApprovalRule $rule)
    {
        $rule->delete();
        return redirect()->back()->with('success', 'Approval rule deleted!');
    }
}
