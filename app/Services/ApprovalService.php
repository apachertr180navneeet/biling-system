<?php

namespace App\Services;

use App\Models\ApprovalRule;
use App\Models\ApprovalRecord;
use App\Models\UserNotification;

class ApprovalService
{
    public static function requestApproval(string $module, int $recordId, float $amount = 0, ?int $requestedBy = null): ?ApprovalRecord
    {
        $requestedBy = $requestedBy ?? auth()->id();

        $rule = ApprovalRule::active()
            ->forModule($module)
            ->orderBy('step_order')
            ->get()
            ->first(function ($rule) use ($amount) {
                return $rule->matchesAmount($amount);
            });

        $record = ApprovalRecord::create([
            'module' => $module,
            'record_id' => $recordId,
            'approval_rule_id' => $rule?->id,
            'requested_by' => $requestedBy,
            'status' => 'pending',
        ]);

        if ($rule && $rule->approver_id) {
            UserNotification::send(
                $rule->approver_id,
                'Approval Required',
                "A {$module} record requires your approval.",
                'warning',
                'approval',
                route('admin.approvals.index')
            );
        } elseif ($rule && $rule->role_id) {
            UserNotification::sendToRole(
                'super-admin',
                'Approval Required',
                "A {$module} record requires approval.",
                'warning',
                'approval',
                route('admin.approvals.index')
            );
        }

        return $record;
    }

    public static function approve(ApprovalRecord $record, ?string $remarks = null): bool
    {
        $record->approve(auth()->user(), $remarks);

        UserNotification::send(
            $record->requested_by,
            'Request Approved',
            "Your {$record->module} request has been approved.",
            'success',
            'approval'
        );

        return true;
    }

    public static function reject(ApprovalRecord $record, ?string $remarks = null): bool
    {
        $record->reject(auth()->user(), $remarks);

        UserNotification::send(
            $record->requested_by,
            'Request Rejected',
            "Your {$record->module} request has been rejected." . ($remarks ? " Reason: {$remarks}" : ''),
            'danger',
            'approval'
        );

        return true;
    }

    public static function getPendingCount(?int $userId = null): int
    {
        $userId = $userId ?? auth()->id();
        return ApprovalRecord::pending()->where('requested_by', $userId)->count();
    }

    public static function getMyPendingApprovals(?int $userId = null): int
    {
        $userId = $userId ?? auth()->id();
        $user = \App\Models\User::find($userId);

        $roleApprovals = ApprovalRecord::pending()
            ->whereHas('approvalRule', function ($q) use ($user) {
                $q->where('role_id', $user->role_id);
            })
            ->count();

        $directApprovals = ApprovalRecord::pending()
            ->whereHas('approvalRule', function ($q) use ($userId) {
                $q->where('approver_id', $userId);
            })
            ->count();

        return $roleApprovals + $directApprovals;
    }
}
