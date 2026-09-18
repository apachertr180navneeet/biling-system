<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyTier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'min_points', 'discount_percentage',
        'points_multiplier', 'description', 'color', 'status',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'discount_percentage' => 'decimal:2',
        'points_multiplier' => 'decimal:2',
    ];

    public function members()
    {
        return $this->hasMany(LoyaltyMember::class);
    }
}
