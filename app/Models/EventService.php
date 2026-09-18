<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'unit_price', 'unit', 'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_event_service', 'event_service_id', 'event_id')
            ->withPivot('quantity', 'unit_price', 'total_price', 'notes')
            ->withTimestamps();
    }
}
