<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryGrn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_grn';

    protected $fillable = [
        'hotel_id', 'purchase_order_id', 'supplier_id', 'grn_number',
        'slug', 'grn_date', 'subtotal', 'tax_amount', 'total_amount',
        'notes', 'remarks', 'grn_status', 'received_by', 'created_by', 'status',
    ];

    protected $casts = [
        'grn_date' => 'date',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(InventoryPurchaseOrder::class, 'purchase_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InventoryGrnItem::class, 'grn_id');
    }
}
