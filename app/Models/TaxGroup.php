<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'status'];

    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'tax_group_tax', 'tax_group_id', 'tax_id')
            ->withPivot('position')
            ->withTimestamps()
            ->orderByPivot('position');
    }

    public function getTotalRateAttribute()
    {
        return $this->taxes->sum('rate');
    }
}
