<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class RestaurantOrder extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'hotel_id', 'order_number', 'slug', 'restaurant_table_id',
        'reservation_id', 'guest_name', 'order_type', 'total_amount',
        'tax_amount', 'discount_amount', 'net_amount', 'payment_method',
        'payment_status', 'notes', 'order_status', 'created_by', 'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function restaurantTable()
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(RestaurantOrderItem::class);
    }

    public function roomServiceCharge()
    {
        return $this->hasOne(RoomServiceCharge::class);
    }
}
