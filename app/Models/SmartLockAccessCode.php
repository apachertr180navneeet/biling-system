<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartLockAccessCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id', 'reservation_id', 'guest_id', 'pin_code',
        'access_type', 'valid_from', 'valid_until', 'is_active',
        'issued_by',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('valid_until', '>=', now());
    }

    public function isValid(): bool
    {
        return $this->is_active && now()->between($this->valid_from, $this->valid_until);
    }
}
