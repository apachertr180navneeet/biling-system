<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryStockAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'item_id', 'adjustment_number', 'slug',
        'adjustment_date', 'adjustment_type', 'quantity_before',
        'adjustment_quantity', 'quantity_after', 'unit_cost',
        'total_value', 'reason', 'approved_by', 'created_by', 'status',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
