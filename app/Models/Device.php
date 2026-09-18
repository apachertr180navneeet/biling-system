<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class Device extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'hotel_id', 'name', 'type', 'brand', 'model', 'serial_number',
        'ip_address', 'port', 'api_key', 'settings', 'location',
        'room_id', 'status', 'last_seen_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'port' => 'integer',
        'last_seen_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function logs()
    {
        return $this->hasMany(DeviceLog::class);
    }

    public function accessCodes()
    {
        return $this->hasMany(SmartLockAccessCode::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }
}
