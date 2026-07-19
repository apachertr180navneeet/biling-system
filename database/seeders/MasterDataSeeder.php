<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\HsnSacMaster;
use App\Models\SparePartCategory;
use App\Models\SparePart;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleVariant;
use App\Models\VehicleColor;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\InvoiceSeries;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ── HSN/SAC Codes ──────────────────────────────────────────────
            $hsnData = [
                ['code' => '850440', 'description' => 'Electrical transformers, static converters (Controllers/Chargers)', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '850152', 'description' => 'Electric motors (Motors)', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '850760', 'description' => 'Lithium-ion accumulators (Batteries)', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '854449', 'description' => 'Wires/cables', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '853890', 'description' => 'Switches', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '851220', 'description' => 'Lighting equipment', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '870830', 'description' => 'Brakes and parts', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '848210', 'description' => 'Ball bearings', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '853690', 'description' => 'Electrical apparatus for switching', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '851230', 'description' => 'Horns', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '854370', 'description' => 'Electrical machines/apparatus', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '853950', 'description' => 'LED lighting', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '850710', 'description' => 'Lead-acid batteries', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '870829', 'description' => 'Other body parts', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '870880', 'description' => 'Suspension/shock absorbers', 'gst_rate' => 18, 'cess_rate' => 0],
                ['code' => '271019', 'description' => 'Lubricants', 'gst_rate' => 18, 'cess_rate' => 0],
            ];

            foreach ($hsnData as $row) {
                HsnSacMaster::updateOrCreate(
                    ['code' => $row['code']],
                    $row
                );
            }

            // ── Spare Part Categories ──────────────────────────────────────
            $category = SparePartCategory::updateOrCreate(
                ['name' => 'EV Components'],
                ['is_active' => true]
            );

            // ── Spare Parts ────────────────────────────────────────────────
            $spareParts = [
                ['part_no' => 'EV-001', 'name' => '0.75MM WIRE', 'hsn_code' => '854449', 'purchase_price' => 5, 'selling_price' => 8, 'mrp' => 10, 'unit' => 'mtr'],
                ['part_no' => 'EV-002', 'name' => '1100W CONTROLLER', 'hsn_code' => '850440', 'purchase_price' => 3200, 'selling_price' => 4500, 'mrp' => 4999],
                ['part_no' => 'EV-003', 'name' => '1500W XP MOTOR 60V', 'hsn_code' => '850152', 'purchase_price' => 6000, 'selling_price' => 8500, 'mrp' => 9499],
                ['part_no' => 'EV-004', 'name' => '48V 30AH LI BATTERY', 'hsn_code' => '850760', 'purchase_price' => 22000, 'selling_price' => 28000, 'mrp' => 30999],
                ['part_no' => 'EV-005', 'name' => 'ALARAM REMOTE', 'hsn_code' => '854370', 'purchase_price' => 250, 'selling_price' => 350, 'mrp' => 399],
                ['part_no' => 'EV-006', 'name' => 'Ball race set', 'hsn_code' => '848210', 'purchase_price' => 200, 'selling_price' => 280, 'mrp' => 320],
                ['part_no' => 'EV-007', 'name' => 'Battery Terminal', 'hsn_code' => '853690', 'purchase_price' => 35, 'selling_price' => 55, 'mrp' => 65],
                ['part_no' => 'EV-008', 'name' => 'Brake Lever', 'hsn_code' => '870830', 'purchase_price' => 80, 'selling_price' => 120, 'mrp' => 149],
                ['part_no' => 'EV-009', 'name' => 'Charger 48V A Star', 'hsn_code' => '850440', 'purchase_price' => 1500, 'selling_price' => 2200, 'mrp' => 2499],
                ['part_no' => 'EV-010', 'name' => 'Controller 60V', 'hsn_code' => '850440', 'purchase_price' => 3500, 'selling_price' => 5000, 'mrp' => 5499],
                ['part_no' => 'EV-011', 'name' => 'Motor 1500W 48V', 'hsn_code' => '850152', 'purchase_price' => 6000, 'selling_price' => 8500, 'mrp' => 9499],
                ['part_no' => 'EV-012', 'name' => 'Mudguard', 'hsn_code' => '870829', 'purchase_price' => 220, 'selling_price' => 320, 'mrp' => 370],
                ['part_no' => 'EV-013', 'name' => 'Tail Light Assembly', 'hsn_code' => '851220', 'purchase_price' => 100, 'selling_price' => 150, 'mrp' => 179],
                ['part_no' => 'EV-014', 'name' => 'Throttle 2 Speed', 'hsn_code' => '853890', 'purchase_price' => 300, 'selling_price' => 430, 'mrp' => 499],
                ['part_no' => 'EV-015', 'name' => 'Horn', 'hsn_code' => '851230', 'purchase_price' => 100, 'selling_price' => 150, 'mrp' => 179],
            ];

            foreach ($spareParts as $sp) {
                SparePart::updateOrCreate(
                    ['part_no' => $sp['part_no']],
                    array_merge($sp, [
                        'category_id' => $category->id,
                        'is_gst_applicable' => true,
                        'gst_rate' => 18,
                        'is_active' => true,
                    ])
                );
            }

            // ── Suppliers ──────────────────────────────────────────────────
            $suppliers = [
                ['name' => 'Auto Parts India Pvt Ltd', 'type' => 'OEM', 'gstin' => '27AABCU9603R1ZM', 'address' => 'Pune, Maharashtra', 'contact_person' => 'Rajesh Kumar', 'phone' => '9876543210', 'email' => 'rajesh@autopartsindia.in'],
                ['name' => 'MotoSpare Distributors', 'type' => 'parts_vendor', 'gstin' => '07AABCU9603R1ZT', 'address' => 'Gurugram, Haryana', 'contact_person' => 'Amit Sharma', 'phone' => '9876543211', 'email' => 'amit@motospare.in'],
                ['name' => 'Genuine Parts Co.', 'type' => 'OEM', 'gstin' => '29AABCU9603R1ZK', 'address' => 'Bengaluru, Karnataka', 'contact_person' => 'Suresh Patel', 'phone' => '9876543212', 'email' => 'suresh@genuineparts.co.in'],
                ['name' => 'Delhi Auto Supplies', 'type' => 'parts_vendor', 'gstin' => '07AABCU9603R1ZL', 'address' => 'New Delhi', 'contact_person' => 'Vikram Singh', 'phone' => '9876543213', 'email' => 'vikram@delhiauto.in'],
                ['name' => 'Southern Spares', 'type' => 'parts_vendor', 'gstin' => '33AABCU9603R1ZM', 'address' => 'Chennai, Tamil Nadu', 'contact_person' => 'Karthik Rajan', 'phone' => '9876543214', 'email' => 'karthik@southernspares.in'],
            ];

            foreach ($suppliers as $s) {
                Supplier::updateOrCreate(
                    ['gstin' => $s['gstin']],
                    $s
                );
            }

            // ── Customers ──────────────────────────────────────────────────
            $customers = [
                ['type' => 'individual', 'first_name' => 'Ankit', 'last_name' => 'Verma', 'phone' => '9910123456', 'email' => 'ankit.verma@email.com', 'address' => '12 MG Road, Pune, Maharashtra', 'state' => 'Maharashtra', 'gstin' => '27ABCPV1234A1ZP'],
                ['type' => 'individual', 'first_name' => 'Priya', 'last_name' => 'Nair', 'phone' => '9910123457', 'email' => 'priya.nair@email.com', 'address' => '45 Anna Salai, Chennai, Tamil Nadu', 'state' => 'Tamil Nadu', 'gstin' => '33ABCPN1234A1ZQ'],
                ['type' => 'corporate', 'first_name' => 'Rahul', 'last_name' => 'Mehta', 'company_name' => 'Green Ride Motors Pvt Ltd', 'phone' => '9910123458', 'email' => 'rahul@greenride.in', 'address' => '78 Nehru Nagar, Ahmedabad, Gujarat', 'state' => 'Gujarat', 'gstin' => '24AABCG1234A1ZR'],
                ['type' => 'individual', 'first_name' => 'Sneha', 'last_name' => 'Iyer', 'phone' => '9910123459', 'email' => 'sneha.iyer@email.com', 'address' => '23 Brigade Road, Bengaluru, Karnataka', 'state' => 'Karnataka', 'gstin' => '29ABCSI1234A1ZS'],
                ['type' => 'individual', 'first_name' => 'Deepak', 'last_name' => 'Gupta', 'phone' => '9910123460', 'email' => 'deepak.gupta@email.com', 'address' => '56 Lajpat Nagar, New Delhi', 'state' => 'Delhi', 'gstin' => '07ABCDG1234A1ZT'],
                ['type' => 'corporate', 'first_name' => 'Neha', 'last_name' => 'Reddy', 'company_name' => 'EV Solutions Hyderabad', 'phone' => '9910123461', 'email' => 'neha@evsolutions.in', 'address' => '89 HiTech City, Hyderabad, Telangana', 'state' => 'Telangana', 'gstin' => '36AABCNR123A1ZU'],
                ['type' => 'individual', 'first_name' => 'Arjun', 'last_name' => 'Rao', 'phone' => '9910123462', 'email' => 'arjun.rao@email.com', 'address' => '34 Salt Lake, Kolkata, West Bengal', 'state' => 'West Bengal', 'gstin' => '19AABCR1234A1ZV'],
                ['type' => 'individual', 'first_name' => 'Kavita', 'last_name' => 'Joshi', 'phone' => '9910123463', 'email' => 'kavita.joshi@email.com', 'address' => '67 FC Road, Pune, Maharashtra', 'state' => 'Maharashtra', 'gstin' => '27AABCK1234A1ZW'],
            ];

            foreach ($customers as $c) {
                Customer::updateOrCreate(
                    ['phone' => $c['phone']],
                    $c
                );
            }

            // ── Vehicle Brands ─────────────────────────────────────────────
            $brands = [
                'Hero Electric' => VehicleBrand::updateOrCreate(['name' => 'Hero Electric'], ['is_active' => true]),
                'Ola Electric' => VehicleBrand::updateOrCreate(['name' => 'Ola Electric'], ['is_active' => true]),
                'Ather Energy' => VehicleBrand::updateOrCreate(['name' => 'Ather Energy'], ['is_active' => true]),
                'TVS Electric' => VehicleBrand::updateOrCreate(['name' => 'TVS Electric'], ['is_active' => true]),
                'Bajaj Electric' => VehicleBrand::updateOrCreate(['name' => 'Bajaj Electric'], ['is_active' => true]),
            ];

            // ── Vehicle Models ─────────────────────────────────────────────
            $models = [];

            $models['Hero Optima'] = VehicleModel::updateOrCreate(
                ['brand_id' => $brands['Hero Electric']->id, 'name' => 'Optima'],
                ['body_type' => 'Scooter', 'is_active' => true]
            );

            $models['Hero Photon'] = VehicleModel::updateOrCreate(
                ['brand_id' => $brands['Hero Electric']->id, 'name' => 'Photon'],
                ['body_type' => 'Scooter', 'is_active' => true]
            );

            $models['Ola S1 Pro'] = VehicleModel::updateOrCreate(
                ['brand_id' => $brands['Ola Electric']->id, 'name' => 'S1 Pro'],
                ['body_type' => 'Scooter', 'is_active' => true]
            );

            $models['Ola S1 Air'] = VehicleModel::updateOrCreate(
                ['brand_id' => $brands['Ola Electric']->id, 'name' => 'S1 Air'],
                ['body_type' => 'Scooter', 'is_active' => true]
            );

            $models['Ather 450X'] = VehicleModel::updateOrCreate(
                ['brand_id' => $brands['Ather Energy']->id, 'name' => '450X'],
                ['body_type' => 'Scooter', 'is_active' => true]
            );

            $models['TVS iQube'] = VehicleModel::updateOrCreate(
                ['brand_id' => $brands['TVS Electric']->id, 'name' => 'iQube'],
                ['body_type' => 'Scooter', 'is_active' => true]
            );

            $models['TVS iQube ST'] = VehicleModel::updateOrCreate(
                ['brand_id' => $brands['TVS Electric']->id, 'name' => 'iQube ST'],
                ['body_type' => 'Scooter', 'is_active' => true]
            );

            $models['Bajaj Chetak'] = VehicleModel::updateOrCreate(
                ['brand_id' => $brands['Bajaj Electric']->id, 'name' => 'Chetak'],
                ['body_type' => 'Scooter', 'is_active' => true]
            );

            // ── Vehicle Variants ───────────────────────────────────────────
            $variants = [];

            $variants['Optima H250'] = VehicleVariant::updateOrCreate(
                ['model_id' => $models['Hero Optima']->id, 'name' => 'H250'],
                ['fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 65990, 'hsn_code' => '871160', 'is_active' => true]
            );

            $variants['Photon HX'] = VehicleVariant::updateOrCreate(
                ['model_id' => $models['Hero Photon']->id, 'name' => 'HX'],
                ['fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 71440, 'hsn_code' => '871160', 'is_active' => true]
            );

            $variants['S1 Pro Gen 2'] = VehicleVariant::updateOrCreate(
                ['model_id' => $models['Ola S1 Pro']->id, 'name' => 'Gen 2'],
                ['fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 129999, 'hsn_code' => '871160', 'is_active' => true]
            );

            $variants['S1 Air'] = VehicleVariant::updateOrCreate(
                ['model_id' => $models['Ola S1 Air']->id, 'name' => 'Standard'],
                ['fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 89999, 'hsn_code' => '871160', 'is_active' => true]
            );

            $variants['450X Gen 3'] = VehicleVariant::updateOrCreate(
                ['model_id' => $models['Ather 450X']->id, 'name' => 'Gen 3'],
                ['fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 118646, 'hsn_code' => '871160', 'is_active' => true]
            );

            $variants['iQube'] = VehicleVariant::updateOrCreate(
                ['model_id' => $models['TVS iQube']->id, 'name' => 'Standard'],
                ['fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 99130, 'hsn_code' => '871160', 'is_active' => true]
            );

            $variants['iQube ST'] = VehicleVariant::updateOrCreate(
                ['model_id' => $models['TVS iQube ST']->id, 'name' => 'ST'],
                ['fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 142690, 'hsn_code' => '871160', 'is_active' => true]
            );

            $variants['Chetak Premium'] = VehicleVariant::updateOrCreate(
                ['model_id' => $models['Bajaj Chetak']->id, 'name' => 'Premium'],
                ['fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 121500, 'hsn_code' => '871160', 'is_active' => true]
            );

            // ── Vehicle Colors ─────────────────────────────────────────────
            $colorSets = [
                $variants['Optima H250']->id => [
                    ['color_name' => 'Black', 'color_code' => '#000000'],
                    ['color_name' => 'White', 'color_code' => '#FFFFFF'],
                ],
                $variants['Photon HX']->id => [
                    ['color_name' => 'Red', 'color_code' => '#CC0000'],
                    ['color_name' => 'Black', 'color_code' => '#000000'],
                ],
                $variants['S1 Pro Gen 2']->id => [
                    ['color_name' => 'Jet Black', 'color_code' => '#1A1A1A'],
                    ['color_name' => 'Coral Storm', 'color_code' => '#FF6B6B'],
                    ['color_name' => 'Hyperlilac', 'color_code' => '#B4A7D6'],
                ],
                $variants['S1 Air']->id => [
                    ['color_name' => 'White', 'color_code' => '#FFFFFF'],
                    ['color_name' => 'Blue', 'color_code' => '#0066CC'],
                ],
                $variants['450X Gen 3']->id => [
                    ['color_name' => 'Space Grey', 'color_code' => '#6E6E6E'],
                    ['color_name' => 'Ather White', 'color_code' => '#F5F5F5'],
                    ['color_name' => 'Still White', 'color_code' => '#E8E8E8'],
                ],
                $variants['iQube']->id => [
                    ['color_name' => 'Pearl White', 'color_code' => '#F0EDE5'],
                    ['color_name' => 'Titanium Grey', 'color_code' => '#6B6B6B'],
                ],
                $variants['iQube ST']->id => [
                    ['color_name' => 'Titanium Grey', 'color_code' => '#6B6B6B'],
                    ['color_name' => 'Copper Bronze', 'color_code' => '#B87333'],
                ],
                $variants['Chetak Premium']->id => [
                    ['color_name' => 'Classic Black', 'color_code' => '#1C1C1C'],
                    ['color_name' => 'Azure Blue', 'color_code' => '#007FFF'],
                    ['color_name' => 'Graceful White', 'color_code' => '#F8F8F8'],
                ],
            ];

            foreach ($colorSets as $variantId => $colors) {
                foreach ($colors as $c) {
                    VehicleColor::updateOrCreate(
                        ['variant_id' => $variantId, 'color_name' => $c['color_name']],
                        $c
                    );
                }
            }

            // ── Service Categories ─────────────────────────────────────────
            $svcCategories = [];

            $svcCategories['Battery Service'] = ServiceCategory::updateOrCreate(
                ['name' => 'Battery Service'],
                ['description' => 'Battery diagnostics, replacement, and maintenance for EVs', 'is_active' => true]
            );

            $svcCategories['Motor Service'] = ServiceCategory::updateOrCreate(
                ['name' => 'Motor Service'],
                ['description' => 'Electric motor inspection, repair, and servicing', 'is_active' => true]
            );

            $svcCategories['Controller Service'] = ServiceCategory::updateOrCreate(
                ['name' => 'Controller Service'],
                ['description' => 'Controller diagnostics, repair, and firmware updates', 'is_active' => true]
            );

            $svcCategories['General EV Service'] = ServiceCategory::updateOrCreate(
                ['name' => 'General EV Service'],
                ['description' => 'General EV maintenance and inspection', 'is_active' => true]
            );

            // ── Services ───────────────────────────────────────────────────
            $services = [
                ['service_category_id' => $svcCategories['Battery Service']->id, 'name' => 'Battery Health Check', 'description' => 'Complete battery health diagnostic and report', 'labor_charge' => 500],
                ['service_category_id' => $svcCategories['Battery Service']->id, 'name' => 'Battery Replacement', 'description' => 'Remove old battery pack and install new one', 'labor_charge' => 1500],
                ['service_category_id' => $svcCategories['Motor Service']->id, 'name' => 'Motor Inspection & Service', 'description' => 'Full motor inspection, lubrication, and performance test', 'labor_charge' => 1200],
                ['service_category_id' => $svcCategories['Motor Service']->id, 'name' => 'Motor Rewinding', 'description' => 'Rewind motor coils for restored performance', 'labor_charge' => 3500],
                ['service_category_id' => $svcCategories['Controller Service']->id, 'name' => 'Controller Diagnostics', 'description' => 'Scan and diagnose controller errors and faults', 'labor_charge' => 800],
                ['service_category_id' => $svcCategories['Controller Service']->id, 'name' => 'Controller Replacement', 'description' => 'Replace faulty controller unit with new one', 'labor_charge' => 2000],
                ['service_category_id' => $svcCategories['General EV Service']->id, 'name' => 'General EV Checkup', 'description' => 'Comprehensive EV health checkup covering all systems', 'labor_charge' => 750],
                ['service_category_id' => $svcCategories['General EV Service']->id, 'name' => 'Brake & Suspension Service', 'description' => 'Brake pad replacement, alignment, and suspension check', 'labor_charge' => 1000],
            ];

            foreach ($services as $svc) {
                Service::updateOrCreate(
                    ['name' => $svc['name']],
                    $svc
                );
            }

            // ── Invoice Series ─────────────────────────────────────────────
            $invoiceSeries = [
                ['type' => 'vehicle', 'prefix' => 'VEH', 'fiscal_year' => '2025-2026', 'last_number' => 0, 'is_active' => true],
                ['type' => 'spare', 'prefix' => 'SPR', 'fiscal_year' => '2025-2026', 'last_number' => 0, 'is_active' => true],
                ['type' => 'service', 'prefix' => 'SVC', 'fiscal_year' => '2025-2026', 'last_number' => 0, 'is_active' => true],
                ['type' => 'vehicle', 'prefix' => 'VEH', 'fiscal_year' => '2026-2027', 'last_number' => 0, 'is_active' => true],
                ['type' => 'spare', 'prefix' => 'SPR', 'fiscal_year' => '2026-2027', 'last_number' => 0, 'is_active' => true],
                ['type' => 'service', 'prefix' => 'SVC', 'fiscal_year' => '2026-2027', 'last_number' => 0, 'is_active' => true],
            ];

            foreach ($invoiceSeries as $is) {
                InvoiceSeries::updateOrCreate(
                    ['type' => $is['type'], 'fiscal_year' => $is['fiscal_year']],
                    $is
                );
            }

        });
    }
}
