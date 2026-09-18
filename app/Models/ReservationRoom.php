<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_id', 'room_id', 'room_type_id', 'rate_plan_id',
        'check_in_date', 'check_out_date', 'rate_per_night',
        'total_amount', 'status',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'rate_per_night' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function ratePlan()
    {
        return $this->belongsTo(RatePlan::class);
    }

    public function getNightsAttribute()
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }
}
