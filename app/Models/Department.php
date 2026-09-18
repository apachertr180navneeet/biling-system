<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['company_id', 'name', 'slug', 'description', 'status'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
}
