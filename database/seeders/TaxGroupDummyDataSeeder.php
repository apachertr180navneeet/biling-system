<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxGroup;
use App\Models\Tax;
use App\Helpers\Helper;

class TaxGroupDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $gst = Tax::where('slug', 'gst')->first();
        $vat = Tax::where('slug', 'vat')->first();
        $serviceTax = Tax::where('slug', 'service-tax')->first();
        $luxuryTax = Tax::where('slug', 'luxury-tax')->first();

        $groups = [
            [
                'name' => 'Room Tariff (GST)',
                'description' => 'Standard GST applicable on room tariff.',
                'taxes' => [$gst->id],
            ],
            [
                'name' => 'F&B (GST + Service Tax)',
                'description' => 'GST and service tax on food & beverage charges.',
                'taxes' => [$gst->id, $serviceTax->id],
            ],
            [
                'name' => 'Room + Luxury (GST + Luxury Tax)',
                'description' => 'GST and luxury tax for premium suite bookings.',
                'taxes' => [$gst->id, $luxuryTax->id],
            ],
            [
                'name' => 'Spa Services (GST + Service Tax)',
                'description' => 'GST and service tax on spa and wellness services.',
                'taxes' => [$gst->id, $serviceTax->id],
            ],
            [
                'name' => 'Banquet (GST + VAT)',
                'description' => 'GST and VAT on banquet and event services.',
                'taxes' => [$gst->id, $vat->id],
            ],
            [
                'name' => 'International Guest (VAT + Luxury Tax)',
                'description' => 'VAT and luxury tax for international guests on select services.',
                'taxes' => [$vat->id, $luxuryTax->id],
            ],
            [
                'name' => 'All Taxes Combined',
                'description' => 'Combined GST, VAT, service tax, and luxury tax for all-inclusive packages.',
                'taxes' => [$gst->id, $vat->id, $serviceTax->id, $luxuryTax->id],
            ],
        ];

        foreach ($groups as $group) {
            $taxGroup = TaxGroup::create([
                'name' => $group['name'],
                'slug' => Helper::slug('tax_groups', $group['name']),
                'description' => $group['description'],
                'status' => 'active',
            ]);

            foreach ($group['taxes'] as $position => $taxId) {
                $taxGroup->taxes()->attach($taxId, ['position' => $position]);
            }
        }
    }
}
