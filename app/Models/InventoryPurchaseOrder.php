<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryPurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'supplier_id', 'po_number', 'slug', 'po_date',
        'expected_delivery_date', 'subtotal', 'tax_amount', 'discount_amount',
        'total_amount', 'notes', 'po_status', 'approved_by', 'approved_at',
        'created_by', 'status',
    ];

    protected $casts = [
        'po_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function supplier()
    {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InventoryPurchaseOrderItem::class, 'purchase_order_id');
    }

    public function grns()
    {
        return $this->hasMany(InventoryGrn::class, 'purchase_order_id');
    }
}
