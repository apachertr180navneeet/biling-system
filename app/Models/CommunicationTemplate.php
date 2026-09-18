<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunicationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'module', 'channel', 'subject', 'body', 'variables',
        'sample_data', 'is_active', 'status',
    ];

    protected $casts = [
        'variables' => 'array',
        'sample_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function logs()
    {
        return $this->hasMany(CommunicationLog::class, 'template_id');
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function render(array $data = []): string
    {
        $body = $this->body;
        foreach ($data as $key => $value) {
            $body = str_replace("{{ {$key} }}", $value, $body);
        }
        return $body;
    }

    public function renderSubject(array $data = []): string
    {
        $subject = $this->subject ?? '';
        foreach ($data as $key => $value) {
            $subject = str_replace("{{ {$key} }}", $value, $subject);
        }
        return $subject;
    }
}
