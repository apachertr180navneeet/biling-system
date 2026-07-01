<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_number', 'customer_id', 'vehicle_stock_id', 'sale_price',
        'booking_date', 'booking_amount', 'allotment_date',
        'registration_date', 'reg_number', 'delivery_date',
        'status', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'allotment_date' => 'date',
            'registration_date' => 'date',
            'delivery_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vehicleStock(): BelongsTo
    {
        return $this->belongsTo(VehicleStock::class, 'vehicle_stock_id');
    }
}
