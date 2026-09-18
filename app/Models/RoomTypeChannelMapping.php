<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypeChannelMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'ota_channel_id', 'room_type_id', 'ota_room_type_id', 'ota_room_name',
        'rate_multiplier', 'sync_rates', 'sync_availability', 'status',
    ];

    protected $casts = [
        'rate_multiplier' => 'decimal:2',
        'sync_rates' => 'boolean',
        'sync_availability' => 'boolean',
    ];

    public function otaChannel()
    {
        return $this->belongsTo(OtaChannel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
