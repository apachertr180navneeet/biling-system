<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id', 'channel', 'recipient', 'subject', 'body',
        'status', 'error_message', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(CommunicationTemplate::class, 'template_id');
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
