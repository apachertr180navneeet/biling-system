<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'building_id', 'floor_id', 'wing_id',
        'room_type_id', 'bed_type_id', 'room_status_id',
        'room_number', 'slug', 'floor_label', 'description', 'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function wing()
    {
        return $this->belongsTo(Wing::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bedType()
    {
        return $this->belongsTo(BedType::class);
    }

    public function roomStatus()
    {
        return $this->belongsTo(RoomStatus::class);
    }

    public function reservations()
    {
        return $this->hasMany(ReservationRoom::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenity')->withTimestamps();
    }
}
