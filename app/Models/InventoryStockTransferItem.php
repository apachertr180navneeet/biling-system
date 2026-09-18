<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id', 'item_id', 'quantity_sent',
        'quantity_received', 'notes',
    ];

    public function transfer()
    {
        return $this->belongsTo(InventoryStockTransfer::class, 'transfer_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
