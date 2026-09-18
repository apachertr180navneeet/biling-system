<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'entry_number', 'entry_date', 'reference_type', 'reference_id',
        'type', 'description', 'total_debit', 'total_credit', 'status',
        'financial_year_id', 'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('entry_date', [$from, $to]);
    }
}
