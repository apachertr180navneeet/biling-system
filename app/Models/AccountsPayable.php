<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsPayable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounts_payable';

    protected $fillable = [
        'hotel_id', 'bill_number', 'vendor_id', 'supplier_id', 'bill_date', 'due_date',
        'subtotal', 'tax_amount', 'total_amount', 'paid_amount', 'balance',
        'reference_type', 'reference_id', 'notes', 'status',
        'financial_year_id', 'journal_entry_id', 'created_by',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function supplier()
    {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id');
    }

    public function payments()
    {
        return $this->hasMany(ApPayment::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', ['approved', 'partial']);
    }
}
