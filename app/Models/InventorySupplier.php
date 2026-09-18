<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventorySupplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'contact_person', 'email', 'phone',
        'address', 'gst_number', 'payment_terms', 'status',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(InventoryPurchaseOrder::class, 'supplier_id');
    }

    public function grns()
    {
        return $this->hasMany(InventoryGrn::class, 'supplier_id');
    }
}
