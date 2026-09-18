<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'asset_code',
        'name',
        'category',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'purchase_cost',
        'warranty_expiry_date',
        'location',
        'status',
        'description',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry_date' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function amcs()
    {
        return $this->hasMany(MaintenanceAmc::class, 'asset_id');
    }

    public function workOrders()
    {
        return $this->hasMany(MaintenanceWorkOrder::class, 'asset_id');
    }
}
