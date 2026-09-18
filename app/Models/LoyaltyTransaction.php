<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loyalty_member_id', 'reservation_id', 'type',
        'points', 'description', 'reference_number', 'status',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function loyaltyMember()
    {
        return $this->belongsTo(LoyaltyMember::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
