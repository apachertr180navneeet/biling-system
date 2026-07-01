<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePartCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'is_active'];

    public function spareParts(): HasMany
    {
        return $this->hasMany(SparePart::class, 'category_id');
    }
}
