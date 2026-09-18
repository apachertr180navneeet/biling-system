<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaignLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'guest_id',
        'recipient_contact',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }
}
