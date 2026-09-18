<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HsnSacCode;

class HsnSacCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            ['code' => '996311', 'name' => 'Room Accommodation Services', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Hotel room accommodation services', 'status' => 'active'],
            ['code' => '996312', 'name' => 'Food and Beverage Services', 'type' => 'sac', 'gst_rate' => 5, 'description' => 'Restaurant and bar food & beverage services', 'status' => 'active'],
            ['code' => '996321', 'name' => 'Event Catering Services', 'type' => 'sac', 'gst_rate' => 5, 'description' => 'Banquet and event catering services', 'status' => 'active'],
            ['code' => '996331', 'name' => 'Conference/Meeting Room Services', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Conference and meeting room hire services', 'status' => 'active'],
            ['code' => '996332', 'name' => 'Spa and Wellness Services', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Spa, massage, and wellness center services', 'status' => 'active'],
            ['code' => '996339', 'name' => 'Other Recreational Services', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Swimming pool, gym, and other recreational services', 'status' => 'active'],
            ['code' => '996341', 'name' => 'Laundry and Dry Cleaning', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Guest laundry and dry cleaning services', 'status' => 'active'],
            ['code' => '996349', 'name' => 'Other Support Services', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Valet parking, concierge, and other support services', 'status' => 'active'],
            ['code' => '997311', 'name' => 'Transport Services', 'type' => 'sac', 'gst_rate' => 5, 'description' => 'Airport transfer and local transport services', 'status' => 'active'],
            ['code' => '997211', 'name' => 'Travel Agent Services', 'type' => 'sac', 'gst_rate' => 5, 'description' => 'Tour packages and travel desk services', 'status' => 'active'],
            ['code' => '998311', 'name' => 'Management Consulting', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Hotel management and consulting services', 'status' => 'active'],
            ['code' => '998511', 'name' => 'Security Services', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Security guard and surveillance services', 'status' => 'active'],
            ['code' => '998611', 'name' => 'Maintenance Services', 'type' => 'sac', 'gst_rate' => 18, 'description' => 'Building and equipment maintenance services', 'status' => 'active'],
            ['code' => '8471', 'name' => 'Computer and IT Services', 'type' => 'hsn', 'gst_rate' => 18, 'description' => 'IT equipment and computer services', 'status' => 'active'],
            ['code' => '9403', 'name' => 'Furniture', 'type' => 'hsn', 'gst_rate' => 18, 'description' => 'Hotel furniture items', 'status' => 'active'],
            ['code' => '9404', 'name' => 'Bedding and Furnishings', 'type' => 'hsn', 'gst_rate' => 12, 'description' => 'Mattresses, pillows, bed linen, and curtains', 'status' => 'active'],
            ['code' => '6912', 'name' => 'Ceramic Tableware', 'type' => 'hsn', 'gst_rate' => 18, 'description' => 'Ceramic plates, cups, and kitchenware', 'status' => 'active'],
            ['code' => '7013', 'name' => 'Glassware', 'type' => 'hsn', 'gst_rate' => 18, 'description' => 'Glass tableware and drinking glasses', 'status' => 'active'],
            ['code' => '7323', 'name' => 'Stainless Steel Utensils', 'type' => 'hsn', 'gst_rate' => 18, 'description' => 'Stainless steel kitchen and serving utensils', 'status' => 'active'],
            ['code' => '6211', 'name' => 'Staff Uniforms', 'type' => 'hsn', 'gst_rate' => 5, 'description' => 'Hotel staff uniforms and workwear', 'status' => 'active'],
        ];

        foreach ($codes as $code) {
            HsnSacCode::updateOrCreate(
                ['code' => $code['code']],
                $code
            );
        }
    }
}
