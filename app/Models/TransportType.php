<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'base_price', 'per_km_rate',
        'per_hour_rate', 'max_passengers', 'status',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'per_km_rate' => 'decimal:2',
        'per_hour_rate' => 'decimal:2',
        'max_passengers' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(TransportBooking::class);
    }
}
