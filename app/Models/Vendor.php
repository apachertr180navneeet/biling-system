<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'vendor_category_id', 'company_name', 'slug',
        'contact_person', 'email', 'phone', 'address',
        'city', 'state', 'pincode',
        'gstin', 'pan', 'cin', 'tan',
        'bank_name', 'bank_account_number', 'bank_ifsc', 'bank_branch',
        'credit_limit', 'payment_terms', 'vendor_type', 'rating',
        'agreement_start_date', 'agreement_end_date', 'notes', 'status',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'agreement_start_date' => 'date',
        'agreement_end_date' => 'date',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id');
    }

    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }
}
