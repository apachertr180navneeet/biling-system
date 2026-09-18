<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'native_name', 'direction', 'is_default', 'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
