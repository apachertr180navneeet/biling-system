<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'name',
        'channel',
        'subject',
        'content',
        'target_audience',
        'scheduled_at',
        'sent_at',
        'status',
        'total_recipients',
        'successful_deliveries',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
        'successful_deliveries' => 'integer',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function logs()
    {
        return $this->hasMany(MarketingCampaignLog::class, 'campaign_id');
    }
}
