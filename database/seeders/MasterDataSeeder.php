<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleVariant;
use App\Models\VehicleColor;
use App\Models\HsnSacMaster;
use App\Models\InvoiceSeries;
use App\Models\SparePartCategory;
use App\Models\SparePart;
use App\Models\Supplier;
use App\Models\Customer;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- Vehicle Brands ---
        $brands = [
            'Maruti Suzuki', 'Hyundai', 'Tata', 'Toyota', 'Honda',
            'Mahindra', 'BMW', 'Mercedes-Benz', 'Kia', 'Volkswagen',
        ];
        foreach ($brands as $b) {
            VehicleBrand::create(['name' => $b]);
        }

        // --- Vehicle Models ---
        $models = [
            ['brand' => 'Maruti Suzuki', 'name' => 'Swift', 'body_type' => 'Hatchback'],
            ['brand' => 'Maruti Suzuki', 'name' => 'Baleno', 'body_type' => 'Hatchback'],
            ['brand' => 'Maruti Suzuki', 'name' => 'Dzire', 'body_type' => 'Sedan'],
            ['brand' => 'Hyundai', 'name' => 'i20', 'body_type' => 'Hatchback'],
            ['brand' => 'Hyundai', 'name' => 'Creta', 'body_type' => 'SUV'],
            ['brand' => 'Hyundai', 'name' => 'Verna', 'body_type' => 'Sedan'],
            ['brand' => 'Tata', 'name' => 'Nexon', 'body_type' => 'SUV'],
            ['brand' => 'Tata', 'name' => 'Tiago', 'body_type' => 'Hatchback'],
            ['brand' => 'Tata', 'name' => 'Harrier', 'body_type' => 'SUV'],
            ['brand' => 'Toyota', 'name' => 'Fortuner', 'body_type' => 'SUV'],
            ['brand' => 'Toyota', 'name' => 'Innova Crysta', 'body_type' => 'MPV'],
            ['brand' => 'Honda', 'name' => 'City', 'body_type' => 'Sedan'],
            ['brand' => 'Honda', 'name' => 'Amaze', 'body_type' => 'Sedan'],
            ['brand' => 'Mahindra', 'name' => 'Scorpio N', 'body_type' => 'SUV'],
            ['brand' => 'Mahindra', 'name' => 'XUV700', 'body_type' => 'SUV'],
        ];
        foreach ($models as $m) {
            $brand = VehicleBrand::where('name', $m['brand'])->first();
            VehicleModel::create([
                'brand_id' => $brand->id,
                'name' => $m['name'],
                'body_type' => $m['body_type'],
            ]);
        }

        // --- Vehicle Variants ---
        $variants = [
            ['model' => 'Swift', 'name' => 'LXi', 'fuel_type' => 'Petrol', 'transmission' => 'Manual', 'ex_showroom_price' => 599000],
            ['model' => 'Swift', 'name' => 'VXi', 'fuel_type' => 'Petrol', 'transmission' => 'Manual', 'ex_showroom_price' => 699000],
            ['model' => 'Swift', 'name' => 'ZXi', 'fuel_type' => 'Petrol', 'transmission' => 'AMT', 'ex_showroom_price' => 849000],
            ['model' => 'Creta', 'name' => 'E', 'fuel_type' => 'Petrol', 'transmission' => 'Manual', 'ex_showroom_price' => 1050000],
            ['model' => 'Creta', 'name' => 'SX', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic', 'ex_showroom_price' => 1590000],
            ['model' => 'Creta', 'name' => 'SXO', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic', 'ex_showroom_price' => 1890000],
            ['model' => 'Nexon', 'name' => 'XM', 'fuel_type' => 'Petrol', 'transmission' => 'Manual', 'ex_showroom_price' => 825000],
            ['model' => 'Nexon', 'name' => 'XZ+', 'fuel_type' => 'Diesel', 'transmission' => 'AMT', 'ex_showroom_price' => 1150000],
            ['model' => 'Nexon', 'name' => 'XM EV', 'fuel_type' => 'Electric', 'transmission' => 'Automatic', 'ex_showroom_price' => 1450000],
            ['model' => 'Fortuner', 'name' => '2.7 AT 4x2', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic', 'ex_showroom_price' => 3390000],
            ['model' => 'Fortuner', 'name' => '2.8 AT 4x4', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic', 'ex_showroom_price' => 4050000],
        ];
        foreach ($variants as $v) {
            $model = VehicleModel::where('name', $v['model'])->first();
            VehicleVariant::create([
                'model_id' => $model->id,
                'name' => $v['name'],
                'fuel_type' => $v['fuel_type'],
                'transmission' => $v['transmission'],
                'ex_showroom_price' => $v['ex_showroom_price'],
            ]);
        }

        // --- Vehicle Colors ---
        $colors = [
            ['model' => 'Swift', 'variant' => 'LXi', 'color_name' => 'Pearl Arctic White', 'color_code' => '#F5F5F5'],
            ['model' => 'Swift', 'variant' => 'LXi', 'color_name' => 'Sizzling Red', 'color_code' => '#E53935'],
            ['model' => 'Swift', 'variant' => 'ZXi', 'color_name' => 'Splash Blue', 'color_code' => '#1E88E5'],
            ['model' => 'Creta', 'variant' => 'SX', 'color_name' => 'Abyss Black', 'color_code' => '#212121'],
            ['model' => 'Creta', 'variant' => 'SX', 'color_name' => 'Titan Grey', 'color_code' => '#757575'],
            ['model' => 'Creta', 'variant' => 'SXO', 'color_name' => 'Deep Forest', 'color_code' => '#2E7D32'],
            ['model' => 'Nexon', 'variant' => 'XZ+', 'color_name' => 'Flame Orange', 'color_code' => '#FB8C00'],
            ['model' => 'Nexon', 'variant' => 'XZ+', 'color_name' => 'Pure Silver', 'color_code' => '#BDBDBD'],
            ['model' => 'Nexon', 'variant' => 'XZ+', 'color_name' => 'Creative Ocean', 'color_code' => '#0277BD'],
            ['model' => 'Fortuner', 'variant' => '2.8 AT 4x4', 'color_name' => 'Phantom Brown', 'color_code' => '#5D4037'],
            ['model' => 'Fortuner', 'variant' => '2.8 AT 4x4', 'color_name' => 'Super White', 'color_code' => '#FFFFFF'],
        ];
        foreach ($colors as $c) {
            $model = VehicleModel::where('name', $c['model'])->first();
            $variant = VehicleVariant::where('model_id', $model->id)->where('name', $c['variant'])->first();
            VehicleColor::create([
                'variant_id' => $variant->id,
                'color_name' => $c['color_name'],
                'color_code' => $c['color_code'],
            ]);
        }

        // --- HSN/SAC Master ---
        $hsnCodes = [
            ['code' => '870321', 'description' => 'Vehicles with spark-ignition engine <= 1000cc', 'gst_rate' => 28, 'cess_rate' => 1],
            ['code' => '870322', 'description' => 'Vehicles with spark-ignition engine 1000-1500cc', 'gst_rate' => 28, 'cess_rate' => 3],
            ['code' => '870323', 'description' => 'Vehicles with spark-ignition engine 1500-3000cc', 'gst_rate' => 28, 'cess_rate' => 5],
            ['code' => '870324', 'description' => 'Vehicles with spark-ignition engine > 3000cc', 'gst_rate' => 28, 'cess_rate' => 7],
            ['code' => '870331', 'description' => 'Vehicles with compression-ignition engine <= 1500cc', 'gst_rate' => 28, 'cess_rate' => 3],
            ['code' => '870332', 'description' => 'Vehicles with compression-ignition engine 1500-3000cc', 'gst_rate' => 28, 'cess_rate' => 5],
            ['code' => '870333', 'description' => 'Vehicles with compression-ignition engine > 3000cc', 'gst_rate' => 28, 'cess_rate' => 7],
            ['code' => '870421', 'description' => 'Diesel powered vehicles for goods transport', 'gst_rate' => 28, 'cess_rate' => 0],
            ['code' => '401110', 'description' => 'Pneumatic tyres for motor cars', 'gst_rate' => 28, 'cess_rate' => 0],
            ['code' => '840734', 'description' => 'Engine parts and accessories', 'gst_rate' => 18, 'cess_rate' => 0],
            ['code' => '851220', 'description' => 'Lighting or visual signalling equipment', 'gst_rate' => 18, 'cess_rate' => 0],
            ['code' => '870810', 'description' => 'Bumpers and parts thereof', 'gst_rate' => 18, 'cess_rate' => 0],
            ['code' => '870829', 'description' => 'Other body parts and accessories', 'gst_rate' => 18, 'cess_rate' => 0],
            ['code' => '870891', 'description' => 'Engine cooling systems', 'gst_rate' => 18, 'cess_rate' => 0],
            ['code' => '902910', 'description' => 'Service parts - labour (supply of service)', 'gst_rate' => 18, 'cess_rate' => 0],
        ];
        foreach ($hsnCodes as $h) {
            HsnSacMaster::create($h);
        }

        // --- Spare Part Categories ---
        $categories = [
            'Engine & Components', 'Brake System', 'Suspension & Steering',
            'Electrical & Electronics', 'Body & Exterior', 'Interior & Trim',
            'Transmission & Clutch', 'Cooling System', 'Exhaust System',
            'Wheels & Tyres', 'Filters & Fluids', 'Lighting',
        ];
        foreach ($categories as $c) {
            SparePartCategory::create(['name' => $c]);
        }

        // --- Spare Parts ---
        $engCat = SparePartCategory::where('name', 'Engine & Components')->first();
        $brakeCat = SparePartCategory::where('name', 'Brake System')->first();
        $suspCat = SparePartCategory::where('name', 'Suspension & Steering')->first();
        $elecCat = SparePartCategory::where('name', 'Electrical & Electronics')->first();
        $filterCat = SparePartCategory::where('name', 'Filters & Fluids')->first();
        $lightCat = SparePartCategory::where('name', 'Lighting')->first();

        $parts = [
            ['part_no' => 'ENG-001', 'name' => 'Oil Filter', 'category_id' => $engCat->id, 'hsn_code' => '840734', 'purchase_price' => 180, 'selling_price' => 350, 'mrp' => 399],
            ['part_no' => 'ENG-002', 'name' => 'Air Filter', 'category_id' => $engCat->id, 'hsn_code' => '840734', 'purchase_price' => 400, 'selling_price' => 750, 'mrp' => 849],
            ['part_no' => 'ENG-003', 'name' => 'Fuel Filter', 'category_id' => $engCat->id, 'hsn_code' => '840734', 'purchase_price' => 350, 'selling_price' => 650, 'mrp' => 749],
            ['part_no' => 'ENG-004', 'name' => 'Spark Plug', 'category_id' => $engCat->id, 'hsn_code' => '840734', 'purchase_price' => 120, 'selling_price' => 250, 'mrp' => 299],
            ['part_no' => 'BRK-001', 'name' => 'Brake Pad Set (Front)', 'category_id' => $brakeCat->id, 'hsn_code' => '870830', 'purchase_price' => 850, 'selling_price' => 1600, 'mrp' => 1799],
            ['part_no' => 'BRK-002', 'name' => 'Brake Pad Set (Rear)', 'category_id' => $brakeCat->id, 'hsn_code' => '870830', 'purchase_price' => 750, 'selling_price' => 1400, 'mrp' => 1599],
            ['part_no' => 'BRK-003', 'name' => 'Brake Disc (Front)', 'category_id' => $brakeCat->id, 'hsn_code' => '870830', 'purchase_price' => 1800, 'selling_price' => 3200, 'mrp' => 3499],
            ['part_no' => 'SUS-001', 'name' => 'Shock Absorber (Front)', 'category_id' => $suspCat->id, 'hsn_code' => '870880', 'purchase_price' => 2200, 'selling_price' => 4000, 'mrp' => 4499],
            ['part_no' => 'SUS-002', 'name' => 'Shock Absorber (Rear)', 'category_id' => $suspCat->id, 'hsn_code' => '870880', 'purchase_price' => 2000, 'selling_price' => 3600, 'mrp' => 3999],
            ['part_no' => 'SUS-003', 'name' => 'Tie Rod End', 'category_id' => $suspCat->id, 'hsn_code' => '870880', 'purchase_price' => 450, 'selling_price' => 900, 'mrp' => 999],
            ['part_no' => 'ELC-001', 'name' => 'Battery 12V 45Ah', 'category_id' => $elecCat->id, 'hsn_code' => '850710', 'purchase_price' => 3200, 'selling_price' => 5200, 'mrp' => 5799],
            ['part_no' => 'ELC-002', 'name' => 'Headlight Bulb (H4)', 'category_id' => $elecCat->id, 'hsn_code' => '853921', 'purchase_price' => 180, 'selling_price' => 350, 'mrp' => 399],
            ['part_no' => 'ELC-003', 'name' => 'Wiper Blade Set', 'category_id' => $elecCat->id, 'hsn_code' => '851240', 'purchase_price' => 250, 'selling_price' => 500, 'mrp' => 549],
            ['part_no' => 'FLT-001', 'name' => 'Engine Oil (5W30) 1L', 'category_id' => $filterCat->id, 'hsn_code' => '271019', 'purchase_price' => 350, 'selling_price' => 650, 'mrp' => 699],
            ['part_no' => 'FLT-002', 'name' => 'Engine Oil (5W30) 5L', 'category_id' => $filterCat->id, 'hsn_code' => '271019', 'purchase_price' => 1600, 'selling_price' => 2800, 'mrp' => 3099],
            ['part_no' => 'FLT-003', 'name' => 'Coolant 1L', 'category_id' => $filterCat->id, 'hsn_code' => '382000', 'purchase_price' => 200, 'selling_price' => 400, 'mrp' => 449],
            ['part_no' => 'LGT-001', 'name' => 'LED Headlight Kit', 'category_id' => $lightCat->id, 'hsn_code' => '853950', 'purchase_price' => 1800, 'selling_price' => 3200, 'mrp' => 3599],
            ['part_no' => 'LGT-002', 'name' => 'Fog Light Assembly', 'category_id' => $lightCat->id, 'hsn_code' => '851220', 'purchase_price' => 1200, 'selling_price' => 2200, 'mrp' => 2499],
        ];
        foreach ($parts as $p) {
            SparePart::create($p);
        }

        // --- Suppliers ---
        $suppliers = [
            ['name' => 'Auto Parts India Pvt Ltd', 'type' => 'OEM', 'gstin' => '27AABCU9603R1ZM', 'address' => 'Plot 45, MIDC Industrial Area, Pune', 'contact_person' => 'Rajesh Sharma', 'phone' => '9876543210', 'email' => 'rajesh@autopartsindia.com'],
            ['name' => 'MotoSpare Distributors', 'type' => 'parts_vendor', 'gstin' => '07AABCU9603R1ZT', 'address' => 'Sector 12, Phase II, Gurugram', 'contact_person' => 'Amit Verma', 'phone' => '9876543211', 'email' => 'amit@motospare.in'],
            ['name' => 'Genuine Parts Co.', 'type' => 'OEM', 'gstin' => '29AABCU9603R1ZK', 'address' => 'Industrial Development Area, Bengaluru', 'contact_person' => 'Suresh Reddy', 'phone' => '9876543212', 'email' => 'suresh@genuineparts.com'],
            ['name' => 'Delhi Auto Supplies', 'type' => 'parts_vendor', 'gstin' => '07AABCU9603R1ZL', 'address' => 'Kashmere Gate, New Delhi', 'contact_person' => 'Vikram Singh', 'phone' => '9876543213', 'email' => 'vikram@delhiauto.co.in'],
            ['name' => 'Southern Spares', 'type' => 'parts_vendor', 'gstin' => '33AABCU9603R1ZM', 'address' => 'Guindy Industrial Estate, Chennai', 'contact_person' => 'Karthik Nair', 'phone' => '9876543214', 'email' => 'karthik@southernspares.com'],
        ];
        foreach ($suppliers as $s) {
            Supplier::create($s);
        }

        // --- Customers ---
        $customers = [
            ['type' => 'individual', 'first_name' => 'Rahul', 'last_name' => 'Sharma', 'phone' => '9812345678', 'email' => 'rahul.sharma@email.com', 'address' => '12A, Green Park Colony', 'state' => 'Delhi', 'gstin' => null, 'pan_no' => null, 'aadhaar_no' => null],
            ['type' => 'individual', 'first_name' => 'Priya', 'last_name' => 'Patel', 'phone' => '9876543219', 'email' => 'priya.patel@email.com', 'address' => '45, Sunrise Apartments', 'state' => 'Gujarat', 'gstin' => null, 'pan_no' => 'ABCDE1234F', 'aadhaar_no' => null],
            ['type' => 'corporate', 'first_name' => 'Vijay', 'last_name' => 'Malhotra', 'company_name' => 'Malhotra Transport Services', 'phone' => '9811112233', 'email' => 'vijay@malhotratransport.com', 'address' => 'Transport Nagar, Sitapura', 'state' => 'Rajasthan', 'gstin' => '08AAAFM1234Q1Z5', 'pan_no' => 'FGHIJ5678K', 'aadhaar_no' => null],
            ['type' => 'corporate', 'first_name' => 'Sunil', 'last_name' => 'Verma', 'company_name' => 'City Cab Operators', 'phone' => '9822223344', 'email' => 'sunil@citycab.in', 'address' => 'HSR Layout, Sector 3', 'state' => 'Karnataka', 'gstin' => '29AAAFV5678R1Z6', 'pan_no' => 'KLMNO9012P', 'aadhaar_no' => null],
            ['type' => 'individual', 'first_name' => 'Ananya', 'last_name' => 'Reddy', 'phone' => '9833334455', 'email' => 'ananya.r@email.com', 'address' => '8-2-293, Jubilee Hills', 'state' => 'Telangana', 'gstin' => null, 'pan_no' => null, 'aadhaar_no' => '123412341234'],
        ];
        foreach ($customers as $c) {
            Customer::create($c);
        }

        // --- Invoice Series ---
        $fy = date('Y') . '-' . substr(date('Y') + 1, -2);
        InvoiceSeries::create(['type' => 'gst', 'prefix' => 'GST', 'fiscal_year' => $fy, 'last_number' => 0]);
        InvoiceSeries::create(['type' => 'non_gst', 'prefix' => 'NGS', 'fiscal_year' => $fy, 'last_number' => 0]);
    }
}
