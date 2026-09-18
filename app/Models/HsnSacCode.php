<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HsnSacCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hsn_sac_codes';

    protected $fillable = [
        'code', 'name', 'type', 'gst_rate', 'cess_rate', 'description', 'status',
    ];

    protected $casts = [
        'gst_rate' => 'decimal:2',
        'cess_rate' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTypeLabelAttribute(): string
    {
        return strtoupper($this->type);
    }
}
