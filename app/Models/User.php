<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Relations\Traits\HasRoles;
use App\Models\Traits\HasActivityLog;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasActivityLog;

    protected $fillable = [
        'first_name', 'last_name', 'full_name', 'slug', 'email', 'phone',
        'password', 'email_verified_at', 'phone_verified_at', 'role', 'role_id',
        'branch_id', 'address', 'area', 'city', 'state', 'country', 'country_code',
        'zipcode', 'latitude', 'longitude', 'timezone', 'avatar', 'bio',
        'device_token', 'device_type', 'status',
    ];

    protected $appends = ['avatar_full_path'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function getAvatarFullPathAttribute()
    {
        if ($this->avatar != '') {
            return asset($this->avatar);
        }
        return "";
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
