<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpareSale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sale_number', 'customer_id', 'sale_date', 'is_gst', 'gst_type', 'subtotal',
        'gst_amount', 'grand_total', 'payment_mode', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'is_gst' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SpareSaleItem::class, 'spare_sale_id');
    }
}
