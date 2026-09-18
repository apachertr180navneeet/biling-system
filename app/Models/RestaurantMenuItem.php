<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'name', 'slug', 'category', 'description',
        'price', 'tax_rate', 'preparation_time', 'is_available', 'status',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
