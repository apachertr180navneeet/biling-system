<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_number', 'hotel_id', 'reservation_id', 'guest_id', 'room_id',
        'category', 'subject', 'description', 'priority', 'status',
        'assigned_to', 'assigned_at', 'started_at', 'completed_at',
        'notes', 'resolution_notes', 'record_status',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public static function generateNumber(): string
    {
        $prefix = 'SR-';
        $today = now()->format('Ymd');
        $last = self::where('request_number', 'like', "{$prefix}{$today}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $sequence = intval(substr($last->request_number, -4)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $today . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
