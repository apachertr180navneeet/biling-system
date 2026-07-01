<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'model_id', 'name', 'fuel_type', 'transmission',
        'ex_showroom_price', 'hsn_code', 'is_active',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function colors(): HasMany
    {
        return $this->hasMany(VehicleColor::class, 'variant_id');
    }
}
