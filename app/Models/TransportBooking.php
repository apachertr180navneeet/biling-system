<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'transport_type_id', 'guest_id', 'room_id', 'booking_number',
        'slug', 'guest_name', 'guest_phone', 'guest_email', 'trip_type',
        'pickup_location', 'drop_location', 'pickup_datetime', 'drop_datetime',
        'estimated_distance_km', 'estimated_hours', 'base_price', 'distance_charges',
        'hourly_charges', 'additional_charges', 'discount_amount', 'tax_amount',
        'total_amount', 'advance_paid', 'driver_name', 'driver_phone',
        'vehicle_number', 'special_instructions', 'status', 'payment_status',
        'is_room_charge', 'created_by',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'drop_datetime' => 'datetime',
        'estimated_distance_km' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'base_price' => 'decimal:2',
        'distance_charges' => 'decimal:2',
        'hourly_charges' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function transportType()
    {
        return $this->belongsTo(TransportType::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getSlugSourceAttribute()
    {
        return $this->booking_number;
    }
}
