<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceSeries extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'prefix', 'fiscal_year', 'last_number', 'is_active'];

    public function nextNumber(): string
    {
        $this->increment('last_number');
        $this->refresh();
        $num = str_pad($this->last_number, 4, '0', STR_PAD_LEFT);
        return "{$this->prefix}/{$this->fiscal_year}/{$num}";
    }
}
