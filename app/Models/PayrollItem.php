<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id', 'employee_id', 'basic_salary', 'earnings', 'deductions',
        'net_pay', 'days_present', 'days_absent', 'overtime_hours',
        'earned_basic', 'earned_hra', 'earned_allowances',
        'pf_deduction', 'esi_deduction', 'tds_deduction', 'other_deductions',
        'status', 'journal_entry_id',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'earnings' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'earned_basic' => 'decimal:2',
        'earned_hra' => 'decimal:2',
        'earned_allowances' => 'decimal:2',
        'pf_deduction' => 'decimal:2',
        'esi_deduction' => 'decimal:2',
        'tds_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
