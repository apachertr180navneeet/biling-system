<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePartStock extends Model
{
    use SoftDeletes;

    protected $fillable = ['spare_part_id', 'quantity', 'min_quantity', 'location', 'is_active'];

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
