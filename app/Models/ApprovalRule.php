<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'module', 'name', 'min_amount', 'max_amount', 'role_id',
        'approver_id', 'step_order', 'require_all', 'status',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'step_order' => 'integer',
        'require_all' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function records()
    {
        return $this->hasMany(ApprovalRecord::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function matchesAmount(float $amount): bool
    {
        if ($this->min_amount !== null && $amount < $this->min_amount) return false;
        if ($this->max_amount !== null && $amount > $this->max_amount) return false;
        return true;
    }
}
