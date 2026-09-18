<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    use HasFactory;

    protected $table = 'timezone';

    protected $fillable = [
        'name', 'label', 'offset', 'offset_minutes', 'status',
    ];

    protected $casts = [
        'offset_minutes' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
