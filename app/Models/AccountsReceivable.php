<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsReceivable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounts_receivable';

    protected $fillable = [
        'hotel_id', 'invoice_number', 'guest_id', 'invoice_date', 'due_date',
        'subtotal', 'tax_amount', 'total_amount', 'received_amount', 'balance',
        'reference_type', 'reference_id', 'notes', 'status',
        'financial_year_id', 'journal_entry_id', 'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function receipts()
    {
        return $this->hasMany(ArReceipt::class);
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
        return $query->whereIn('status', ['sent', 'partial']);
    }
}
