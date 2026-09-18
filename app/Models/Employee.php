<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActivityLog;

class Employee extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'hotel_id', 'employee_id', 'first_name', 'last_name', 'slug', 'email',
        'phone', 'date_of_birth', 'gender', 'address', 'city', 'state', 'pin_code',
        'pan_number', 'aadhaar_number', 'department_id', 'designation_id', 'branch_id',
        'date_of_joining', 'date_of_leaving', 'basic_salary', 'employment_type',
        'bank_name', 'bank_account_number', 'ifsc_code', 'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'date_of_leaving' => 'date',
        'basic_salary' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class);
    }

    public function payrollItems()
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
