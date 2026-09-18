<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class Reservation extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'reservation_number', 'hotel_id', 'guest_id', 'booking_source',
        'ota_name', 'ota_channel_id', 'ota_reservation_id', 'ota_reservation_status',
        'check_in_date', 'check_out_date',
        'actual_check_in', 'actual_check_out', 'adults', 'children',
        'total_amount', 'paid_amount', 'discount_amount', 'tax_amount',
        'status', 'is_group_booking', 'group_name', 'corporate_name',
        'notes', 'created_by', 'record_status',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'actual_check_in' => 'datetime',
        'actual_check_out' => 'datetime',
        'adults' => 'integer',
        'children' => 'integer',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'is_group_booking' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function otaChannel()
    {
        return $this->belongsTo(OtaChannel::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(OtaSyncLog::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rooms()
    {
        return $this->hasMany(ReservationRoom::class);
    }

    public function payments()
    {
        return $this->hasMany(ReservationPayment::class);
    }

    public function checkIn()
    {
        return $this->hasOne(CheckIn::class);
    }

    public function checkOut()
    {
        return $this->hasOne(CheckOut::class);
    }

    public function getNightsAttribute()
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public static function generateNumber()
    {
        $series = NumberSeries::where('module', 'reservations')->where('status', 'active')->first();
        if ($series) {
            return $series->generateNumber();
        }
        $prefix = 'RES-';
        $last = self::withTrashed()->orderBy('id', 'desc')->first();
        $nextNumber = $last ? intval(substr($last->reservation_number, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
