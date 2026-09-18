<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hall extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'name', 'slug', 'description', 'capacity', 'area_sqft',
        'base_price', 'price_unit', 'floor', 'is_ac', 'has_projector',
        'has_stage', 'has_sound_system', 'has_parking', 'image_path', 'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'area_sqft' => 'decimal:2',
        'base_price' => 'decimal:2',
        'is_ac' => 'boolean',
        'has_projector' => 'boolean',
        'has_stage' => 'boolean',
        'has_sound_system' => 'boolean',
        'has_parking' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(HallAmenity::class, 'hall_amenity', 'hall_id', 'hall_amenity_id')
            ->withPivot('additional_cost')
            ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
