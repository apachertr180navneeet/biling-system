<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_id', 'payment_date', 'amount', 'payment_method',
        'reference_number', 'notes', 'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
