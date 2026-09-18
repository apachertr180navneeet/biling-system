<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RatePlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'room_type_id', 'name', 'rate_per_night',
        'effective_from', 'effective_to', 'min_stay', 'max_stay',
        'description', 'status',
    ];

    protected $casts = [
        'rate_per_night' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'min_stay' => 'integer',
        'max_stay' => 'integer',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
