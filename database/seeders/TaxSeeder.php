<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tax;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            ['name' => 'GST', 'slug' => 'gst', 'rate' => 10, 'type' => 'percentage', 'is_default' => true],
            ['name' => 'VAT', 'slug' => 'vat', 'rate' => 15, 'type' => 'percentage'],
            ['name' => 'Service Tax', 'slug' => 'service-tax', 'rate' => 5, 'type' => 'percentage'],
            ['name' => 'Luxury Tax', 'slug' => 'luxury-tax', 'rate' => 18, 'type' => 'percentage'],
        ];

        foreach ($taxes as $tax) {
            Tax::updateOrCreate(
                ['slug' => $tax['slug']],
                $tax
            );
        }
    }
}
