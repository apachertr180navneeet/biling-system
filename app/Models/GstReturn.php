<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GstReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'return_number', 'period', 'return_type', 'filing_date',
        'total_taxable_value', 'total_cgst', 'total_sgst', 'total_igst',
        'total_cess', 'total_tax', 'status', 'created_by',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'total_taxable_value' => 'decimal:2',
        'total_cgst' => 'decimal:2',
        'total_sgst' => 'decimal:2',
        'total_igst' => 'decimal:2',
        'total_cess' => 'decimal:2',
        'total_tax' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
