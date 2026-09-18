<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'name', 'slug', 'description', 'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function floors()
    {
        return $this->hasMany(Floor::class);
    }
}
