<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleColor extends Model
{
    use SoftDeletes;

    protected $fillable = ['variant_id', 'color_name', 'color_code', 'is_active'];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(VehicleVariant::class, 'variant_id');
    }
}
