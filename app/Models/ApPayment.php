<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'payment_number', 'accounts_payable_id', 'payment_date',
        'amount', 'payment_method', 'reference_number', 'bank_account_id',
        'notes', 'journal_entry_id', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function accountsPayable()
    {
        return $this->belongsTo(AccountsPayable::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'bank_account_id');
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
