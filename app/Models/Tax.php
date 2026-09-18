<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'rate', 'type', 'is_default', 'status'];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    public function taxGroups()
    {
        return $this->belongsToMany(TaxGroup::class, 'tax_group_tax', 'tax_id', 'tax_group_id')
            ->withPivot('position')
            ->withTimestamps();
    }
}
