<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtaSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ota_channel_id', 'reservation_id', 'direction', 'action',
        'request_payload', 'response_payload', 'status', 'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function otaChannel()
    {
        return $this->belongsTo(OtaChannel::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
