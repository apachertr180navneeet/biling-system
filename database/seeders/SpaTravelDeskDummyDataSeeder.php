<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SpaService;
use App\Models\SpaAppointment;
use App\Models\TransportType;
use App\Models\TransportBooking;
use App\Models\Hotel;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Support\Str;

class SpaTravelDeskDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::first();
        if (!$hotel) return;

        // --- Spa Services ---
        $services = [
            ['name' => 'Swedish Massage', 'slug' => 'swedish-massage', 'description' => 'Full body relaxation massage', 'duration_minutes' => 60, 'price' => 3500, 'category' => 'Massage', 'gender' => 'unisex', 'status' => 'active'],
            ['name' => 'Deep Tissue Massage', 'slug' => 'deep-tissue-massage', 'description' => 'Therapeutic deep pressure massage', 'duration_minutes' => 60, 'price' => 4000, 'category' => 'Massage', 'gender' => 'unisex', 'status' => 'active'],
            ['name' => 'Thai Massage', 'slug' => 'thai-massage', 'description' => 'Traditional Thai stretching massage', 'duration_minutes' => 75, 'price' => 4500, 'category' => 'Massage', 'gender' => 'unisex', 'status' => 'active'],
            ['name' => 'Aromatherapy Massage', 'slug' => 'aromatherapy-massage', 'description' => 'Essential oils infused massage', 'duration_minutes' => 60, 'price' => 4200, 'category' => 'Massage', 'gender' => 'unisex', 'status' => 'active'],
            ['name' => 'Hot Stone Massage', 'slug' => 'hot-stone-massage', 'description' => 'Heated stone therapy massage', 'duration_minutes' => 90, 'price' => 5500, 'category' => 'Massage', 'gender' => 'unisex', 'status' => 'active'],
            ['name' => 'Classic Facial', 'slug' => 'classic-facial', 'description' => 'Deep cleansing facial treatment', 'duration_minutes' => 45, 'price' => 2500, 'category' => 'Facial', 'gender' => 'unisex', 'status' => 'active'],
            ['name' => 'Anti-Aging Facial', 'slug' => 'anti-aging-facial', 'description' => 'Advanced anti-aging skin treatment', 'duration_minutes' => 60, 'price' => 3800, 'category' => 'Facial', 'gender' => 'female', 'status' => 'active'],
            ['name' => 'Body Scrub', 'slug' => 'body-scrub', 'description' => 'Exfoliating body treatment', 'duration_minutes' => 45, 'price' => 3000, 'category' => 'Body Treatment', 'gender' => 'unisex', 'status' => 'active'],
            ['name' => 'Manicure', 'slug' => 'manicure', 'description' => 'Premium hand nail care', 'duration_minutes' => 30, 'price' => 1200, 'category' => 'Nail Care', 'gender' => 'female', 'status' => 'active'],
            ['name' => 'Pedicure', 'slug' => 'pedicure', 'description' => 'Premium foot nail care', 'duration_minutes' => 45, 'price' => 1500, 'category' => 'Nail Care', 'gender' => 'female', 'status' => 'active'],
        ];

        foreach ($services as $svc) {
            $svc['hotel_id'] = $hotel->id;
            SpaService::firstOrCreate(['slug' => $svc['slug']], $svc);
        }

        $guestNames = ['Rahul Sharma', 'Priya Patel', 'Amit Singh', 'Sneha Gupta', 'Vikram Joshi', 'Neha Agarwal', 'Rohan Mehta', 'Pooja Verma'];
        $statuses = ['pending', 'confirmed', 'completed'];
        $paymentStatuses = ['unpaid', 'partial', 'paid'];
        $therapists = ['Raj Kumar', 'Sunita Devi', 'Aarti Sharma', 'Mohan Lal'];

        $userId = \App\Models\User::first()?->id;
        $apptServices = SpaService::where('hotel_id', $hotel->id)->get();
        $rooms = Room::where('hotel_id', $hotel->id)->get();
        $guests = Guest::where('status', 'active')->limit(5)->get();

        for ($i = 0; $i < 12; $i++) {
            $svc = $apptServices->random();
            $date = now()->addDays(rand(-5, 10));
            $price = $svc->price;
            $discount = rand(0, 2) ? $price * 0.1 : 0;
            $taxable = $price - $discount;
            $tax = $taxable * 0.18;
            $total = $taxable + $tax;
            $advance = in_array(rand(0, 2), [0]) ? 0 : ($advance = rand(0, 2) ? $total : $total * 0.5);
            $status = $statuses[array_rand($statuses)];
            $payStatus = $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid');
            $apptNum = 'SPA-' . $date->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            SpaAppointment::firstOrCreate(['slug' => $apptNum], [
                'hotel_id' => $hotel->id,
                'spa_service_id' => $svc->id,
                'guest_id' => $guests->isNotEmpty() ? $guests->random()->id : null,
                'room_id' => $rooms->isNotEmpty() ? $rooms->random()->id : null,
                'appointment_number' => $apptNum,
                'slug' => $apptNum,
                'guest_name' => $guestNames[array_rand($guestNames)],
                'guest_phone' => '98' . rand(10000000, 99999999),
                'guest_email' => strtolower(Str::random(6)) . '@email.com',
                'appointment_date' => $date->format('Y-m-d'),
                'appointment_time' => sprintf('%02d:%02d', rand(9, 18), [0, 30][rand(0, 1)]),
                'duration_minutes' => $svc->duration_minutes,
                'price' => $price,
                'discount_amount' => $discount,
                'tax_amount' => round($tax, 2),
                'total_amount' => round($total, 2),
                'advance_paid' => round($advance, 2),
                'therapist_name' => $therapists[array_rand($therapists)],
                'status' => $status,
                'payment_status' => $payStatus,
                'special_requests' => rand(0, 1) ? 'Gentle pressure please' : null,
                'internal_notes' => null,
                'created_by' => $userId,
                'is_room_charge' => 'no',
            ]);
        }

        // --- Transport Types ---
        $types = [
            ['name' => 'Sedan', 'slug' => 'sedan', 'description' => 'Comfortable 4-seater sedan', 'base_price' => 500, 'per_km_rate' => 12, 'per_hour_rate' => 200, 'max_passengers' => 4, 'status' => 'active'],
            ['name' => 'SUV', 'slug' => 'suv', 'description' => 'Spacious 6-seater SUV', 'base_price' => 800, 'per_km_rate' => 18, 'per_hour_rate' => 300, 'max_passengers' => 6, 'status' => 'active'],
            ['name' => 'Innova', 'slug' => 'innova', 'description' => 'Toyota Innova 7-seater', 'base_price' => 1000, 'per_km_rate' => 20, 'per_hour_rate' => 350, 'max_passengers' => 7, 'status' => 'active'],
            ['name' => 'Tempo Traveller', 'slug' => 'tempo-traveller', 'description' => '12-seater mini bus', 'base_price' => 2000, 'per_km_rate' => 30, 'per_hour_rate' => 500, 'max_passengers' => 12, 'status' => 'active'],
            ['name' => 'Luxury Sedan', 'slug' => 'luxury-sedan', 'description' => 'Premium luxury sedan', 'base_price' => 1500, 'per_km_rate' => 25, 'per_hour_rate' => 400, 'max_passengers' => 4, 'status' => 'active'],
            ['name' => 'Auto Rickshaw', 'slug' => 'auto-rickshaw', 'description' => '3-wheeler for short distances', 'base_price' => 100, 'per_km_rate' => 15, 'per_hour_rate' => 0, 'max_passengers' => 3, 'status' => 'active'],
        ];

        foreach ($types as $type) {
            TransportType::firstOrCreate(['slug' => $type['slug']], $type);
        }

        $pickups = ['Hotel Main Gate', 'Airport Terminal 1', 'Railway Station', 'City Center Mall', 'Convention Center'];
        $drops = ['Airport Terminal 2', 'Hotel Main Gate', 'Shopping District', 'Business Park', 'Beach Resort'];
        $driverNames = ['Suresh Yadav', 'Mahesh Tiwari', 'Vijay Kumar', 'Prakash Singh'];
        $vehicles = ['MH 12 AB 1234', 'MH 01 CD 5678', 'MH 14 EF 9012', 'MH 02 GH 3456'];
        $tripTypes = ['pickup', 'drop', 'round_trip', 'hourly'];

        $allTypes = TransportType::all();

        for ($i = 0; $i < 10; $i++) {
            $type = $allTypes->random();
            $date = now()->addDays(rand(0, 10));
            $dist = rand(5, 50);
            $hrs = rand(1, 6);
            $distanceCharges = $dist * $type->per_km_rate;
            $hourlyCharges = $hrs * $type->per_hour_rate;
            $additional = rand(0, 1) ? rand(100, 500) : 0;
            $discount = rand(0, 1) ? 100 : 0;
            $subtotal = $type->base_price + $distanceCharges + $hourlyCharges + $additional - $discount;
            $tax = $subtotal * 0.18;
            $total = $subtotal + $tax;
            $advance = rand(0, 2) ? $total : ($total * 0.5);
            $status = $statuses[array_rand($statuses)];
            $payStatus = $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid');
            $trvNum = 'TRV-' . $date->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            TransportBooking::firstOrCreate(['slug' => $trvNum], [
                'hotel_id' => $hotel->id,
                'transport_type_id' => $type->id,
                'guest_id' => $guests->isNotEmpty() ? $guests->random()->id : null,
                'room_id' => $rooms->isNotEmpty() ? $rooms->random()->id : null,
                'booking_number' => $trvNum,
                'slug' => $trvNum,
                'guest_name' => $guestNames[array_rand($guestNames)],
                'guest_phone' => '98' . rand(10000000, 99999999),
                'guest_email' => strtolower(Str::random(6)) . '@email.com',
                'trip_type' => $tripTypes[array_rand($tripTypes)],
                'pickup_location' => $pickups[array_rand($pickups)],
                'drop_location' => $drops[array_rand($drops)],
                'pickup_datetime' => $date->format('Y-m-d') . ' ' . sprintf('%02d:%02d', rand(5, 20), [0, 30][rand(0, 1)]),
                'estimated_distance_km' => $dist,
                'estimated_hours' => $hrs,
                'base_price' => $type->base_price,
                'distance_charges' => $distanceCharges,
                'hourly_charges' => $hourlyCharges,
                'additional_charges' => $additional,
                'discount_amount' => $discount,
                'tax_amount' => round($tax, 2),
                'total_amount' => round($total, 2),
                'advance_paid' => round($advance, 2),
                'driver_name' => $driverNames[array_rand($driverNames)],
                'driver_phone' => '90' . rand(10000000, 99999999),
                'vehicle_number' => $vehicles[array_rand($vehicles)],
                'special_instructions' => rand(0, 1) ? 'Airport pickup, arrive 15 min early' : null,
                'status' => $status,
                'payment_status' => $payStatus,
                'is_room_charge' => 'no',
                'created_by' => $userId,
            ]);
        }

        $this->command->info('Spa & Travel Desk dummy data seeded successfully!');
    }
}
