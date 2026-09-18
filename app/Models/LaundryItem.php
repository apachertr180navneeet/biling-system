<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaundryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'name', 'slug', 'item_type', 'quantity',
        'unit', 'description', 'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function laundryOrders()
    {
        return $this->belongsToMany(LaundryOrder::class, 'laundry_order_items')
            ->withPivot('quantity_sent', 'quantity_received', 'damage_count', 'notes')
            ->withTimestamps();
    }
}
