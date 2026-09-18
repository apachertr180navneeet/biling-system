<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpaAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'spa_service_id', 'guest_id', 'room_id', 'appointment_number',
        'slug', 'guest_name', 'guest_phone', 'guest_email', 'appointment_date',
        'appointment_time', 'duration_minutes', 'price', 'discount_amount',
        'tax_amount', 'total_amount', 'advance_paid', 'therapist_name',
        'status', 'payment_status', 'special_requests', 'internal_notes',
        'created_by', 'is_room_charge',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function spaService()
    {
        return $this->belongsTo(SpaService::class);
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
        return $this->appointment_number;
    }
}
