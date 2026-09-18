<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryStockTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'transfer_number', 'slug', 'transfer_date',
        'from_location', 'to_location', 'notes', 'transfer_status',
        'approved_by', 'approved_at', 'created_by', 'status',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
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
        return $this->hasMany(InventoryStockTransferItem::class, 'transfer_id');
    }
}
