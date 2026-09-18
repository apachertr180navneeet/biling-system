<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    public $timestamps = false;

    protected $fillable = [
        'log_name', 'description', 'subject_type', 'subject_id', 'event',
        'old_values', 'new_values', 'causer_id', 'causer_type',
        'properties', 'ip_address', 'user_agent', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->morphTo();
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('properties->module', $module);
    }

    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    public function scopeBySubject($query, $type, $id)
    {
        return $query->where('subject_type', $type)->where('subject_id', $id);
    }
}
