<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number', 'invoice_type', 'customer_id', 'vehicle_stock_id',
        'invoice_date', 'is_gst', 'gst_type', 'subtotal', 'gst_amount',
        'cess_amount', 'total_amount', 'round_off', 'grand_total',
        'status', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'is_gst' => 'boolean',
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

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}
