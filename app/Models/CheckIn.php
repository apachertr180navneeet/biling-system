<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class CheckIn extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'reservation_id', 'hotel_id', 'room_id', 'key_card_numbers',
        'id_verified', 'id_document_type', 'id_document_number',
        'id_document_expiry', 'arrival_time', 'special_requests',
        'notes', 'checked_in_by', 'status',
    ];

    protected $casts = [
        'id_document_expiry' => 'date',
        'arrival_time' => 'datetime',
        'id_verified' => 'boolean',
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

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
