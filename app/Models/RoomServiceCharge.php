<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomServiceCharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'reservation_id', 'restaurant_order_id',
        'charge_number', 'slug', 'amount', 'tax_amount', 'total_amount',
        'posted_by', 'posted_at', 'notes', 'charge_status', 'status',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function restaurantOrder()
    {
        return $this->belongsTo(RestaurantOrder::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
