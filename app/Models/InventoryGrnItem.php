<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryGrnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_id', 'item_id', 'quantity_ordered', 'quantity_received',
        'quantity_accepted', 'quantity_rejected', 'unit_cost',
        'total_cost', 'rejection_reason',
    ];

    public function grn()
    {
        return $this->belongsTo(InventoryGrn::class, 'grn_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
