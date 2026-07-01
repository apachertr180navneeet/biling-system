<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleStock extends Model
{
    use SoftDeletes;

    protected $fillable = ['chassis_number', 'engine_number', 'color_id', 'mfg_year', 'purchase_date', 'purchase_price', 'status', 'notes', 'is_active'];

    public function color(): BelongsTo
    {
        return $this->belongsTo(VehicleColor::class, 'color_id');
    }
}
