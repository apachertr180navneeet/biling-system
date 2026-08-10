<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $fillable = [
        'payable_type',
        'payable_id',
        'transaction_type',
        'amount',
        'payment_mode',
        'reference_no',
        'note',
        'created_by',
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePayments($query)
    {
        return $query->where('transaction_type', 'payment');
    }

    public function scopeRollbacks($query)
    {
        return $query->where('transaction_type', 'rollback');
    }
}
