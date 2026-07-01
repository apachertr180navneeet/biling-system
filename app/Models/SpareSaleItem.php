<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpareSaleItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'spare_sale_id', 'spare_part_id', 'part_name', 'hsn_code',
        'quantity', 'rate', 'gst_rate', 'gst_amount', 'total', 'is_active',
    ];

    public function spareSale(): BelongsTo
    {
        return $this->belongsTo(SpareSale::class, 'spare_sale_id');
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
