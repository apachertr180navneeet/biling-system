<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id', 'table_number', 'slug', 'capacity',
        'floor_number', 'section', 'table_status', 'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function orders()
    {
        return $this->hasMany(RestaurantOrder::class);
    }
}
