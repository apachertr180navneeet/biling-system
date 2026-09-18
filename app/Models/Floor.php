<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Floor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'building_id', 'name', 'slug', 'floor_number', 'description', 'status',
    ];

    protected $casts = [
        'floor_number' => 'integer',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function wings()
    {
        return $this->hasMany(Wing::class);
    }
}
