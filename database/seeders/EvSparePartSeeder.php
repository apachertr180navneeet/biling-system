<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SparePartCategory;
use App\Models\SparePart;

class EvSparePartSeeder extends Seeder
{
    public function run(): void
    {
        $evCat = SparePartCategory::firstOrCreate(['name' => 'EV Components']);

        $evParts = [
            ['part_no' => 'EV-001', 'name' => '0.75MM WIRE', 'category_id' => $evCat->id, 'hsn_code' => '854449', 'purchase_price' => 5, 'selling_price' => 8, 'mrp' => 10, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-002', 'name' => '1100W CONTROLLER', 'category_id' => $evCat->id, 'hsn_code' => '850440', 'purchase_price' => 3200, 'selling_price' => 4500, 'mrp' => 4999, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-003', 'name' => '1500W XP MOTOR 60V', 'category_id' => $evCat->id, 'hsn_code' => '850152', 'purchase_price' => 6000, 'selling_price' => 8500, 'mrp' => 9499, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-004', 'name' => '48V 30AH LI BATTERY', 'category_id' => $evCat->id, 'hsn_code' => '850760', 'purchase_price' => 22000, 'selling_price' => 28000, 'mrp' => 30999, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-005', 'name' => 'ALARAM REMOTE', 'category_id' => $evCat->id, 'hsn_code' => '854370', 'purchase_price' => 250, 'selling_price' => 350, 'mrp' => 399, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-006', 'name' => 'Ball race set', 'category_id' => $evCat->id, 'hsn_code' => '848210', 'purchase_price' => 200, 'selling_price' => 280, 'mrp' => 320, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-007', 'name' => 'Battery Terminal', 'category_id' => $evCat->id, 'hsn_code' => '853690', 'purchase_price' => 35, 'selling_price' => 55, 'mrp' => 65, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-008', 'name' => 'Brake Lever', 'category_id' => $evCat->id, 'hsn_code' => '870830', 'purchase_price' => 80, 'selling_price' => 120, 'mrp' => 149, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-009', 'name' => 'Charger 48V A Star', 'category_id' => $evCat->id, 'hsn_code' => '850440', 'purchase_price' => 1500, 'selling_price' => 2200, 'mrp' => 2499, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-010', 'name' => 'Controller 60V', 'category_id' => $evCat->id, 'hsn_code' => '850440', 'purchase_price' => 3500, 'selling_price' => 5000, 'mrp' => 5499, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-011', 'name' => 'Motor 1500W 48V', 'category_id' => $evCat->id, 'hsn_code' => '850152', 'purchase_price' => 6000, 'selling_price' => 8500, 'mrp' => 9499, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-012', 'name' => 'Mudguard', 'category_id' => $evCat->id, 'hsn_code' => '870829', 'purchase_price' => 220, 'selling_price' => 320, 'mrp' => 370, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-013', 'name' => 'Tail Light Assembly', 'category_id' => $evCat->id, 'hsn_code' => '851220', 'purchase_price' => 100, 'selling_price' => 150, 'mrp' => 179, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-014', 'name' => 'Throttle 2 Speed', 'category_id' => $evCat->id, 'hsn_code' => '853890', 'purchase_price' => 300, 'selling_price' => 430, 'mrp' => 499, 'gst_rate' => 18, 'is_gst_applicable' => true],
            ['part_no' => 'EV-015', 'name' => 'Horn', 'category_id' => $evCat->id, 'hsn_code' => '851230', 'purchase_price' => 100, 'selling_price' => 150, 'mrp' => 179, 'gst_rate' => 18, 'is_gst_applicable' => true],
        ];

        foreach ($evParts as $p) {
            SparePart::updateOrCreate(['part_no' => $p['part_no']], $p);
        }

        $this->command->info('EV spare parts seeded successfully.');
    }
}
