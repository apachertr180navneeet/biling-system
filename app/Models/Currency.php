<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'name', 'symbol', 'exchange_rate', 'is_default', 'status'];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_default' => 'boolean',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
