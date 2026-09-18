<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGateway extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'logo', 'mode',
        'is_active', 'credentials', 'settings', 'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credentials' => 'array',
        'settings' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function getCredential($key, $default = null)
    {
        return data_get($this->credentials, $key, $default);
    }

    public function getSetting($key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }
}
