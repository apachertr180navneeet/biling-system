<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'hall_id', 'guest_id', 'event_number', 'slug',
        'event_name', 'event_type', 'contact_name', 'contact_phone', 'contact_email',
        'event_date', 'start_time', 'end_time', 'expected_guests', 'actual_guests',
        'hall_charges', 'services_charges', 'additional_charges', 'discount_amount',
        'tax_amount', 'total_amount', 'advance_paid', 'balance_amount',
        'special_requests', 'internal_notes', 'booking_status', 'payment_status',
        'created_by', 'approved_by', 'approved_at', 'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'expected_guests' => 'integer',
        'actual_guests' => 'integer',
        'hall_charges' => 'decimal:2',
        'services_charges' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function services()
    {
        return $this->belongsToMany(EventService::class, 'event_event_service', 'event_id', 'event_service_id')
            ->withPivot('quantity', 'unit_price', 'total_price', 'notes')
            ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
