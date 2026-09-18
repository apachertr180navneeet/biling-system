<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'module', 'record_id', 'approval_rule_id', 'requested_by',
        'approved_by', 'status', 'remarks', 'approved_at', 'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function approvalRule()
    {
        return $this->belongsTo(ApprovalRule::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(User $approver, ?string $remarks = null): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'remarks' => $remarks,
            'approved_at' => now(),
        ]);
    }

    public function reject(User $approver, ?string $remarks = null): bool
    {
        return $this->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'remarks' => $remarks,
            'rejected_at' => now(),
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForModule($query, $module, $recordId)
    {
        return $query->where('module', $module)->where('record_id', $recordId);
    }
}
