<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendorCategory;
use App\Models\Vendor;
use App\Models\Hotel;
use App\Helpers\Helper;

class VendorDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        if (!$hotel) {
            $this->command->error('No active hotel found.');
            return;
        }

        if (VendorCategory::count() > 0) {
            $this->command->info('Vendor data already exists, skipping.');
            return;
        }

        $categoriesData = [
            ['name' => 'Food & Beverage Supplier', 'description' => 'Suppliers for food and beverage items'],
            ['name' => 'Housekeeping Supplier', 'description' => 'Suppliers for cleaning and housekeeping supplies'],
            ['name' => 'Equipment & Machinery', 'description' => 'Suppliers for equipment, machinery, and spare parts'],
            ['name' => 'Technology & IT', 'description' => 'Suppliers for software, hardware, and IT services'],
            ['name' => 'Construction & Maintenance', 'description' => 'Vendors for construction, renovation, and maintenance'],
            ['name' => 'Security Services', 'description' => 'Security guard and surveillance service providers'],
            ['name' => 'Laundry & Linen', 'description' => 'Laundry services and linen suppliers'],
            ['name' => 'Guest Amenities', 'description' => 'Suppliers for toiletries, slippers, robes, and guest items'],
            ['name' => 'Transport & Logistics', 'description' => 'Transport, cab, and logistics service providers'],
            ['name' => 'Legal & Consulting', 'description' => 'Legal, audit, and consulting service providers'],
        ];

        $categories = collect();
        foreach ($categoriesData as $cat) {
            $categories->push(VendorCategory::create([
                'name' => $cat['name'],
                'slug' => Helper::slug('vendor_categories', $cat['name']),
                'description' => $cat['description'],
                'status' => 'active',
            ]));
        }

        $vendorsData = [
            ['cat' => 'Food & Beverage Supplier', 'type' => 'material', 'company' => 'Fresh Farms Supply Co.', 'contact' => 'Rajesh Kumar', 'email' => 'rajesh@freshfarms.com', 'phone' => '9876543210', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'gstin' => '27AABCT1234F1Z5', 'credit' => 50000, 'terms' => 'Net 30', 'rating' => 'excellent'],
            ['cat' => 'Food & Beverage Supplier', 'type' => 'material', 'company' => 'Mega Grocery Wholesalers', 'contact' => 'Amit Patel', 'email' => 'amit@megagrocery.com', 'phone' => '9876543212', 'city' => 'Pune', 'state' => 'Maharashtra', 'gstin' => '27AABCM9012H1Z1', 'credit' => 75000, 'terms' => 'Net 30', 'rating' => 'good'],
            ['cat' => 'Beverage Distributors', 'type' => 'material', 'company' => 'Beverage Bros Distributors', 'contact' => 'Vikram Singh', 'email' => 'vikram@beveragebros.com', 'phone' => '9876543213', 'city' => 'Bangalore', 'state' => 'Karnataka', 'gstin' => '29AABCB3456J1Z9', 'credit' => 100000, 'terms' => 'COD', 'rating' => 'good'],
            ['cat' => 'Housekeeping Supplier', 'type' => 'material', 'company' => 'CleanPro Supplies Pvt Ltd', 'contact' => 'Neha Gupta', 'email' => 'neha@cleanpro.com', 'phone' => '9876543214', 'city' => 'Chennai', 'state' => 'Tamil Nadu', 'gstin' => '33AABCC7890K1Z7', 'credit' => 30000, 'terms' => 'Net 30', 'rating' => 'average'],
            ['cat' => 'Equipment & Machinery', 'type' => 'both', 'company' => 'Star Kitchen Solutions', 'contact' => 'Arjun Mehta', 'email' => 'arjun@starkitchen.com', 'phone' => '9876543215', 'city' => 'Hyderabad', 'state' => 'Telangana', 'gstin' => '36AABCS1234L1Z5', 'credit' => 200000, 'terms' => 'Net 45', 'rating' => 'excellent'],
            ['cat' => 'Guest Amenities', 'type' => 'material', 'company' => 'Guest Comfort Products', 'contact' => 'Kavita Joshi', 'email' => 'kavita@guestcomfort.com', 'phone' => '9876543216', 'city' => 'Jaipur', 'state' => 'Rajasthan', 'gstin' => '08AABCG5678M1Z3', 'credit' => 40000, 'terms' => 'Net 15', 'rating' => 'good'],
            ['cat' => 'Technology & IT', 'type' => 'service', 'company' => 'TechVista Solutions', 'contact' => 'Suresh Reddy', 'email' => 'suresh@techvista.com', 'phone' => '9876543218', 'city' => 'Bangalore', 'state' => 'Karnataka', 'gstin' => '29AABCT5678P1Z1', 'credit' => 150000, 'terms' => 'Net 30', 'rating' => 'excellent'],
            ['cat' => 'Security Services', 'type' => 'service', 'company' => 'GuardForce Security', 'contact' => 'Mohammed Ali', 'email' => 'ali@guardforce.com', 'phone' => '9876543219', 'city' => 'Delhi', 'state' => 'Delhi', 'gstin' => '07AABCG9012Q1Z9', 'credit' => 80000, 'terms' => 'Net 15', 'rating' => 'average'],
            ['cat' => 'Laundry & Linen', 'type' => 'both', 'company' => 'Hotel Linen House', 'contact' => 'Priya Sharma', 'email' => 'priya@linenhouse.com', 'phone' => '9876543211', 'city' => 'Delhi', 'state' => 'Delhi', 'gstin' => '07AABCL5678G1Z3', 'credit' => 60000, 'terms' => 'Net 30', 'rating' => 'good'],
            ['cat' => 'Construction & Maintenance', 'type' => 'service', 'company' => 'BuildRight Contractors', 'contact' => 'Vijay Nair', 'email' => 'vijay@buildright.com', 'phone' => '9876543220', 'city' => 'Kochi', 'state' => 'Kerala', 'gstin' => '32AABCB3456R1Z7', 'credit' => 300000, 'terms' => 'Net 45', 'rating' => 'poor'],
            ['cat' => 'Transport & Logistics', 'type' => 'service', 'company' => 'SwiftMove Logistics', 'contact' => 'Deepak Jain', 'email' => 'deepak@swiftmove.com', 'phone' => '9876543221', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'gstin' => '24AABCS7890S1Z5', 'credit' => 25000, 'terms' => 'COD', 'rating' => 'good'],
            ['cat' => 'Legal & Consulting', 'type' => 'service', 'company' => 'Singh & Associates', 'contact' => 'Advocate Singh', 'email' => 'singh@legal.com', 'phone' => '9876543222', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'gstin' => '27AABCL1234T1Z3', 'credit' => 50000, 'terms' => 'Net 15', 'rating' => 'excellent'],
        ];

        foreach ($vendorsData as $v) {
            $cat = $categories->firstWhere('name', $v['cat']);
            Vendor::create([
                'hotel_id' => $hotel->id,
                'vendor_category_id' => $cat?->id,
                'company_name' => $v['company'],
                'slug' => Helper::slug('vendors', $v['company']),
                'contact_person' => $v['contact'],
                'email' => $v['email'],
                'phone' => $v['phone'],
                'address' => '123 Business Park',
                'city' => $v['city'],
                'state' => $v['state'],
                'pincode' => '400001',
                'gstin' => $v['gstin'],
                'pan' => substr($v['gstin'], 2, 10),
                'vendor_type' => $v['type'],
                'credit_limit' => $v['credit'],
                'payment_terms' => $v['terms'],
                'rating' => $v['rating'],
                'agreement_start_date' => now()->subYear(),
                'agreement_end_date' => now()->addYear(),
                'notes' => 'Standard vendor agreement',
                'status' => 'active',
            ]);
        }

        $this->command->info('Vendor dummy data seeded successfully!');
        $this->command->info('  - Categories: ' . VendorCategory::count());
        $this->command->info('  - Vendors: ' . Vendor::count());
    }
}
