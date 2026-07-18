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
        'invoice_number', 'invoice_type', 'customer_id',
        'vehicle_description', 'chassis_number', 'engine_number', 'mfg_year',
        'vehicle_inventory_id',
        'invoice_date', 'is_gst', 'gst_type', 'subtotal', 'gst_amount',
        'cgst_amount', 'sgst_amount', 'igst_amount',
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



    public function vehicleInventory(): BelongsTo
    {
        return $this->belongsTo(VehicleInventory::class, 'vehicle_inventory_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}
