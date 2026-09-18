<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaundryOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'order_number', 'slug', 'order_date',
        'expected_return_date', 'actual_return_date', 'vendor_name',
        'total_items', 'total_weight', 'total_cost', 'notes',
        'order_status', 'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function items()
    {
        return $this->belongsToMany(LaundryItem::class, 'laundry_order_items')
            ->withPivot('quantity_sent', 'quantity_received', 'damage_count', 'notes')
            ->withTimestamps();
    }
}
