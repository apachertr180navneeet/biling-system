<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class Company extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'name', 'slug', 'logo', 'email', 'phone', 'website',
        'address', 'city', 'state', 'country', 'zipcode',
        'gstin', 'pan', 'tan', 'state_code', 'state_name',
        'is_gst_registered', 'is_einvoice_enabled',
        'currency_id', 'timezone', 'status',
        'about', 'tagline', 'founding_year',
        'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url',
    ];

    protected $casts = [
        'is_gst_registered' => 'boolean',
        'is_einvoice_enabled' => 'boolean',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function financialYears()
    {
        return $this->hasMany(FinancialYear::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getCurrentFinancialYear()
    {
        return $this->financialYears()->where('is_current', true)->first();
    }
}
