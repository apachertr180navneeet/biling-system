<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id', 'event_type', 'user_id', 'employee_id',
        'reservation_id', 'data', 'status', 'message',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function scopeByEvent($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }
}
