<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceWorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'work_order_number',
        'asset_id',
        'title',
        'type',
        'priority',
        'assigned_employee_id',
        'schedule_date',
        'completion_date',
        'cost',
        'description',
        'status',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function asset()
    {
        return $this->belongsTo(MaintenanceAsset::class, 'asset_id');
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }
}
