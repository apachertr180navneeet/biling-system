<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'part_no', 'name', 'category', 'hsn_code',
        'selling_price', 'mrp', 'min_stock', 'is_active',
    ];

    public function getHsnSacCodeAttribute(): ?string
    {
        return $this->hsn_code;
    }

    public function getHsnAttribute(): ?string
    {
        return $this->hsn_code;
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SparePartStockTransaction::class, 'spare_part_id');
    }
}
