<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteFeature extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['icon', 'title', 'description', 'section', 'status', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scopeForSection($query, string $section)
    {
        return $query->where('section', $section)->where('status', 'active')->orderBy('sort_order');
    }
}
