<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NumberSeries extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['prefix', 'suffix', 'next_number', 'padding', 'module', 'description', 'status'];

    protected $casts = [
        'next_number' => 'integer',
        'padding' => 'integer',
    ];

    public function generateNumber()
    {
        $number = str_pad($this->next_number, $this->padding, '0', STR_PAD_LEFT);
        $formatted = $this->prefix . $number . $this->suffix;

        $this->increment('next_number');

        return $formatted;
    }

    public function peekNextNumber()
    {
        $number = str_pad($this->next_number, $this->padding, '0', STR_PAD_LEFT);
        return $this->prefix . $number . $this->suffix;
    }
}
