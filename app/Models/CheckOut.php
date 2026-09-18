<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class CheckOut extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'reservation_id', 'hotel_id', 'room_id', 'check_out_time',
        'final_bill_amount', 'total_charges', 'total_payments',
        'balance_due', 'room_condition', 'damage_notes',
        'damage_charges', 'feedback_rating', 'feedback_notes',
        'checked_out_by', 'status',
    ];

    protected $casts = [
        'check_out_time' => 'datetime',
        'final_bill_amount' => 'decimal:2',
        'total_charges' => 'decimal:2',
        'total_payments' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'damage_charges' => 'decimal:2',
        'feedback_rating' => 'integer',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }
}
