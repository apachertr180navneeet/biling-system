<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class Guest extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'first_name', 'last_name', 'slug', 'email', 'phone',
        'id_type', 'id_number', 'address', 'city', 'state',
        'country', 'zipcode', 'nationality', 'company_name',
        'notes', 'status',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function profile()
    {
        return $this->hasOne(GuestProfile::class);
    }

    public function loyaltyMember()
    {
        return $this->hasOne(LoyaltyMember::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
