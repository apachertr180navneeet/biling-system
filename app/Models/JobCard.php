<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_card_number', 'customer_id', 'vehicle_stock_id', 'vehicle_number',
        'vehicle_model', 'kilometer_reading', 'complaint', 'status',
        'total_labor', 'total_parts', 'subtotal', 'is_gst', 'gst_type',
        'gst_amount', 'cess_amount', 'grand_total', 'service_date',
        'completion_date', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return ['service_date' => 'date', 'completion_date' => 'date'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vehicleStock(): BelongsTo
    {
        return $this->belongsTo(VehicleStock::class, 'vehicle_stock_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(JobCardService::class, 'job_card_id');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(JobCardPart::class, 'job_card_id');
    }
}
