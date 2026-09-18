<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guest_id', 'date_of_birth', 'gender', 'photo_path',
        'id_type', 'id_number', 'id_expiry_date', 'id_document_path',
        'address_proof_type', 'address_proof_path', 'occupation',
        'dietary_preference', 'room_preference', 'bed_preference',
        'pillow_preference', 'arrival_preference', 'communication_preference',
        'special_notes', 'vip_status', 'vip_level', 'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'id_expiry_date' => 'date',
        'vip_status' => 'boolean',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
