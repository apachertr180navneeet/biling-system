<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guest_id', 'loyalty_tier_id', 'member_number',
        'total_points', 'total_stays', 'total_spent',
        'enrolled_date', 'last_activity_date', 'status',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'total_stays' => 'integer',
        'total_spent' => 'decimal:2',
        'enrolled_date' => 'date',
        'last_activity_date' => 'date',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function loyaltyTier()
    {
        return $this->belongsTo(LoyaltyTier::class);
    }

    public function transactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public static function generateMemberNumber()
    {
        $prefix = 'LOY-';
        $last = self::withTrashed()->orderBy('id', 'desc')->first();
        $nextNumber = $last ? intval(substr($last->member_number, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
