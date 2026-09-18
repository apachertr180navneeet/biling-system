<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'slug', 'email', 'phone',
        'address', 'city', 'state', 'country', 'zipcode',
        'description', 'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
