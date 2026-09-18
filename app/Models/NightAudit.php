<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NightAudit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'audit_date', 'hotel_id', 'total_rooms_occupied',
        'total_rooms_available', 'total_revenue', 'total_payments_received',
        'total_outstanding', 'room_charges_posted', 'tax_charges_posted',
        'complimentary_rooms', 'no_show_count', 'cancellation_count',
        'walk_in_count', 'status', 'notes', 'audited_by',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'total_rooms_occupied' => 'integer',
        'total_rooms_available' => 'integer',
        'total_revenue' => 'decimal:2',
        'total_payments_received' => 'decimal:2',
        'total_outstanding' => 'decimal:2',
        'room_charges_posted' => 'decimal:2',
        'tax_charges_posted' => 'decimal:2',
        'complimentary_rooms' => 'integer',
        'no_show_count' => 'integer',
        'cancellation_count' => 'integer',
        'walk_in_count' => 'integer',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function auditedBy()
    {
        return $this->belongsTo(User::class, 'audited_by');
    }
}
