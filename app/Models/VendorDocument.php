<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id', 'document_name', 'document_type', 'document_number',
        'issue_date', 'expiry_date', 'file_path', 'notes', 'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
