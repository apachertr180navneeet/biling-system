<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class Expense extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'hotel_id', 'expense_number', 'account_id', 'vendor_id', 'expense_date',
        'amount', 'tax_amount', 'total_amount', 'payment_method',
        'description', 'notes', 'reference_type', 'reference_id',
        'journal_entry_id', 'created_by', 'status',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
