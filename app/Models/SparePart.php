<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'part_no', 'name', 'category_id', 'hsn_code',
        'is_gst_applicable', 'gst_rate', 'purchase_price',
        'selling_price', 'unit', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_gst_applicable' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SparePartCategory::class, 'category_id');
    }
}
