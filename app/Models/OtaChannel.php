<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtaChannel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'name', 'provider', 'api_key', 'api_secret',
        'property_id_on_ota', 'endpoint_url', 'sync_rates', 'sync_availability',
        'sync_reservations', 'auto_sync', 'last_synced_at', 'status',
    ];

    protected $casts = [
        'sync_rates' => 'boolean',
        'sync_availability' => 'boolean',
        'sync_reservations' => 'boolean',
        'auto_sync' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key', 'api_secret',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomTypeMappings()
    {
        return $this->hasMany(RoomTypeChannelMapping::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(OtaSyncLog::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
