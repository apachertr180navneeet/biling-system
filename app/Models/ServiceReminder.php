<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceReminder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'vehicle_number', 'last_service_date',
        'next_service_date', 'reminder_date', 'status', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_service_date' => 'date',
            'next_service_date' => 'date',
            'reminder_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
