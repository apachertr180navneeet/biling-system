<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'floor_id', 'name', 'slug', 'description', 'status',
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }
}
