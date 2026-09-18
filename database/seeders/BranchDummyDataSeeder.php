<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Helpers\Helper;

class BranchDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        if (\App\Models\Company::count() === 0) {
            \App\Models\Company::create([
                'id' => 1,
                'name' => 'Mehmaan Group',
                'slug' => 'mehmaan-group',
                'email' => 'info@mehmaan.com',
                'phone' => '+91-11-2345-6789',
                'website' => 'https://mehmaan.com',
                'address' => '10 Park Avenue',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'country' => 'India',
                'zipcode' => '110001',
                'currency_id' => \App\Models\Currency::where('code', 'INR')->first()?->id ?? 1,
                'timezone' => 'Asia/Kolkata',
                'status' => 'active',
            ]);
        }

        if (\App\Models\Hotel::count() === 0) {
            \App\Models\Hotel::create([
                'company_id' => 1,
                'name' => 'Mehmaan Grand Hotel',
                'slug' => 'mehmaan-grand-hotel',
                'email' => 'grandhotel@mehmaan.com',
                'phone' => '+91-11-9876-5432',
                'address' => '1 Rajpath Road',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'country' => 'India',
                'zipcode' => '110001',
                'description' => 'Flagship luxury hotel of the Mehmaan Group',
                'status' => 'active',
            ]);
        }

        $branches = [
            [
                'company_id' => 1,
                'name' => 'Mehmaan Hospitality - Head Office',
                'slug' => Helper::slug('branches', 'Mehmaan Hospitality - Head Office'),
                'email' => 'headoffice@mehmaan.com',
                'phone' => '+91-11-4567-8900',
                'address' => '42 Connaught Place',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'country' => 'India',
                'zipcode' => '110001',
                'is_head_office' => true,
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'name' => 'Mehmaan Resort Goa',
                'slug' => Helper::slug('branches', 'Mehmaan Resort Goa'),
                'email' => 'goa@mehmaan.com',
                'phone' => '+91-832-2345-678',
                'address' => '15 Candolim Beach Road, Bardez',
                'city' => 'Goa',
                'state' => 'Goa',
                'country' => 'India',
                'zipcode' => '403515',
                'is_head_office' => false,
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'name' => 'Mehmaan Suites Mumbai',
                'slug' => Helper::slug('branches', 'Mehmaan Suites Mumbai'),
                'email' => 'mumbai@mehmaan.com',
                'phone' => '+91-22-6789-012',
                'address' => '88 Bandra West, Hill Road',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'zipcode' => '400050',
                'is_head_office' => false,
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'name' => 'Mehmaan Grand Jaipur',
                'slug' => Helper::slug('branches', 'Mehmaan Grand Jaipur'),
                'email' => 'jaipur@mehmaan.com',
                'phone' => '+91-141-3456-789',
                'address' => '23 Hawa Mahal Road, Badi Choupad',
                'city' => 'Jaipur',
                'state' => 'Rajasthan',
                'country' => 'India',
                'zipcode' => '302002',
                'is_head_office' => false,
                'status' => 'active',
            ],
            [
                'company_id' => 1,
                'name' => 'Mehmaan Lakeside Udaipur',
                'slug' => Helper::slug('branches', 'Mehmaan Lakeside Udaipur'),
                'email' => 'udaipur@mehmaan.com',
                'phone' => '+91-294-5678-901',
                'address' => '5 Fateh Sagar Lake Road',
                'city' => 'Udaipur',
                'state' => 'Rajasthan',
                'country' => 'India',
                'zipcode' => '313001',
                'is_head_office' => false,
                'status' => 'inactive',
            ],
            [
                'company_id' => 1,
                'name' => 'Mehmaan Heritage Varanasi',
                'slug' => Helper::slug('branches', 'Mehmaan Heritage Varanasi'),
                'email' => 'varanasi@mehmaan.com',
                'phone' => '+91-542-2345-678',
                'address' => '12 Assi Ghat, Varanasi',
                'city' => 'Varanasi',
                'state' => 'Uttar Pradesh',
                'country' => 'India',
                'zipcode' => '221005',
                'is_head_office' => false,
                'status' => 'active',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
