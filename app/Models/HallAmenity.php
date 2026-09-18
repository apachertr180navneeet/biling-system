<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HallAmenity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'status',
    ];

    public function halls()
    {
        return $this->belongsToMany(Hall::class, 'hall_amenity', 'hall_amenity_id', 'hall_id')
            ->withPivot('additional_cost')
            ->withTimestamps();
    }
}
