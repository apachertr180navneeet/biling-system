<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'base_rate', 'max_occupancy', 'description', 'status',
    ];

    protected $casts = [
        'base_rate' => 'decimal:2',
        'max_occupancy' => 'integer',
    ];
}
