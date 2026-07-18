<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCardPart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_card_id', 'spare_part_id', 'part_name', 'hsn_code',
        'quantity', 'rate', 'gst_rate', 'gst_amount',
        'cgst_amount', 'sgst_amount', 'igst_amount',
        'total', 'is_active',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class, 'job_card_id');
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
