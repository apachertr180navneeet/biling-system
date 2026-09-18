<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_order_id', 'restaurant_menu_item_id', 'quantity',
        'unit_price', 'total_price', 'special_instructions', 'item_status',
    ];

    public function order()
    {
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'restaurant_menu_item_id');
    }
}
