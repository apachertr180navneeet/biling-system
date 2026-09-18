<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hall;
use App\Models\HallAmenity;
use App\Models\Event;
use App\Models\EventService;
use App\Models\Hotel;
use App\Helpers\Helper;
use Carbon\Carbon;

class BanquetDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        if (!$hotel) {
            $this->command->error('No active hotel found.');
            return;
        }

        if (Hall::count() > 0) {
            $this->command->info('Banquet data already exists, skipping.');
            return;
        }

        $amenitiesData = [
            ['name' => 'Wi-Fi', 'description' => 'High-speed wireless internet'],
            ['name' => 'Projector & Screen', 'description' => 'HD projector with large screen'],
            ['name' => 'Sound System', 'description' => 'Professional PA sound system'],
            ['name' => 'Microphones', 'description' => 'Wireless handheld microphones'],
            ['name' => 'Stage Setup', 'description' => 'Raised platform stage with curtains'],
            ['name' => 'LED Lighting', 'description' => 'Customizable LED lighting'],
            ['name' => 'AC', 'description' => 'Air conditioning'],
            ['name' => 'Parking', 'description' => 'Dedicated parking space'],
            ['name' => 'Catering Kitchen', 'description' => 'Separate kitchen for caterers'],
            ['name' => 'Green Room', 'description' => 'Private room for performers/VIPs'],
            ['name' => 'Dance Floor', 'description' => 'Polished wooden dance floor'],
            ['name' => 'Valet Service', 'description' => 'Valet parking service'],
        ];

        $amenities = collect();
        foreach ($amenitiesData as $a) {
            $amenities->push(HallAmenity::create([
                'name' => $a['name'],
                'slug' => Helper::slug('hall_amenities', $a['name']),
                'description' => $a['description'],
                'status' => 'active',
            ]));
        }

        $hallsData = [
            ['name' => 'Grand Ballroom', 'capacity' => 500, 'area' => 5000, 'price' => 200000, 'floor' => 'Ground Floor', 'ac' => true, 'projector' => true, 'stage' => true, 'sound' => true, 'parking' => true],
            ['name' => 'Crystal Hall', 'capacity' => 200, 'area' => 2500, 'price' => 100000, 'floor' => '1st Floor', 'ac' => true, 'projector' => true, 'stage' => false, 'sound' => true, 'parking' => true],
            ['name' => 'Imperial Room', 'capacity' => 100, 'area' => 1200, 'price' => 50000, 'floor' => '2nd Floor', 'ac' => true, 'projector' => true, 'stage' => false, 'sound' => true, 'parking' => false],
            ['name' => 'Garden Pavilion', 'capacity' => 300, 'area' => 4000, 'price' => 150000, 'floor' => 'Outdoor', 'ac' => false, 'projector' => false, 'stage' => true, 'sound' => true, 'parking' => true],
            ['name' => 'Board Room', 'capacity' => 30, 'area' => 500, 'price' => 20000, 'floor' => '3rd Floor', 'ac' => true, 'projector' => true, 'stage' => false, 'sound' => false, 'parking' => false],
            ['name' => 'Poolside Deck', 'capacity' => 80, 'area' => 1000, 'price' => 40000, 'floor' => 'Outdoor', 'ac' => false, 'projector' => false, 'stage' => false, 'sound' => true, 'parking' => true],
        ];

        $halls = collect();
        foreach ($hallsData as $h) {
            $hall = Hall::create([
                'hotel_id' => $hotel->id,
                'name' => $h['name'],
                'slug' => Helper::slug('halls', $h['name']),
                'capacity' => $h['capacity'],
                'area_sqft' => $h['area'],
                'base_price' => $h['price'],
                'price_unit' => 'per_event',
                'floor' => $h['floor'],
                'is_ac' => $h['ac'],
                'has_projector' => $h['projector'],
                'has_stage' => $h['stage'],
                'has_sound_system' => $h['sound'],
                'has_parking' => $h['parking'],
                'status' => 'active',
            ]);
            $hall->amenities()->sync($amenities->random(rand(3, 6))->pluck('id')->toArray());
            $halls->push($hall);
        }

        $servicesData = [
            ['name' => 'Catering - Veg Menu', 'price' => 800, 'unit' => 'per_person'],
            ['name' => 'Catering - Non-Veg Menu', 'price' => 1200, 'unit' => 'per_person'],
            ['name' => 'Catering - Premium Menu', 'price' => 2000, 'unit' => 'per_person'],
            ['name' => 'Decoration - Standard', 'price' => 25000, 'unit' => 'per_event'],
            ['name' => 'Decoration - Premium', 'price' => 50000, 'unit' => 'per_event'],
            ['name' => 'Photography', 'price' => 30000, 'unit' => 'per_event'],
            ['name' => 'Videography', 'price' => 40000, 'unit' => 'per_event'],
            ['name' => 'DJ Service', 'price' => 20000, 'unit' => 'per_event'],
            ['name' => 'Live Band', 'price' => 50000, 'unit' => 'per_event'],
            ['name' => 'Valet Service', 'price' => 10000, 'unit' => 'per_event'],
            ['name' => 'Security Team', 'price' => 15000, 'unit' => 'per_event'],
            ['name' => 'Floral Arrangements', 'price' => 20000, 'unit' => 'per_event'],
        ];

        $services = collect();
        foreach ($servicesData as $s) {
            $services->push(EventService::create([
                'name' => $s['name'],
                'slug' => Helper::slug('event_services', $s['name']),
                'unit_price' => $s['price'],
                'unit' => $s['unit'],
                'status' => 'active',
            ]));
        }

        $eventTypes = ['wedding', 'conference', 'seminar', 'corporate', 'social', 'birthday', 'other'];
        $statuses = ['inquiry', 'proposed', 'confirmed', 'completed', 'cancelled'];
        $contactNames = ['Amit Sharma', 'Priya Patel', 'Vikram Singh', 'Neha Gupta', 'Rajesh Kumar', 'Sunita Verma', 'Deepak Joshi', 'Anita Desai', 'Sanjay Rao', 'Meena Iyer'];

        for ($i = 0; $i < 10; $i++) {
            $eventDate = Carbon::now()->addDays(rand(-30, 60));
            $hall = $halls->random();
            $eventType = $eventTypes[array_rand($eventTypes)];
            $bookingStatus = $statuses[array_rand($statuses)];
            $expectedGuests = rand(50, min(300, $hall->capacity));
            $hallCharges = $hall->base_price;
            $numServices = rand(1, 4);
            $selectedServices = $services->random($numServices);
            $servicesCharges = 0;
            $serviceSyncData = [];

            foreach ($selectedServices as $svc) {
                $qty = $svc->unit === 'per_person' ? $expectedGuests : 1;
                $total = $svc->unit_price * $qty;
                $servicesCharges += $total;
                $serviceSyncData[$svc->id] = [
                    'quantity' => $qty,
                    'unit_price' => $svc->unit_price,
                    'total_price' => $total,
                ];
            }

            $additionalCharges = rand(0, 20000);
            $discount = rand(0, 10000);
            $subtotal = $hallCharges + $servicesCharges + $additionalCharges - $discount;
            $tax = round($subtotal * 0.18, 2);
            $total = $subtotal + $tax;
            $advance = $bookingStatus === 'completed' ? $total : ($bookingStatus === 'confirmed' ? rand(floor($total * 0.3), floor($total * 0.5)) : rand(0, floor($total * 0.3)));

            $eventNumber = 'EVT-' . $eventDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $contact = $contactNames[array_rand($contactNames)];

            $event = Event::create([
                'hotel_id' => $hotel->id,
                'hall_id' => $hall->id,
                'event_number' => $eventNumber,
                'slug' => Helper::slug('events', $eventType . '-' . ($i + 1)),
                'event_name' => ucfirst($eventType) . ' - ' . $contact,
                'event_type' => $eventType,
                'contact_name' => $contact,
                'contact_phone' => '98765' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                'contact_email' => strtolower(str_replace(' ', '.', $contact)) . '@email.com',
                'event_date' => $eventDate,
                'start_time' => sprintf('%02d:00', rand(9, 18)),
                'end_time' => sprintf('%02d:00', rand(19, 23)),
                'expected_guests' => $expectedGuests,
                'hall_charges' => $hallCharges,
                'services_charges' => $servicesCharges,
                'additional_charges' => $additionalCharges,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'advance_paid' => $advance,
                'balance_amount' => $total - $advance,
                'special_requests' => collect(['Veg only please', 'Need extra lighting', 'Live music required', 'VIP seating area', null])->random(),
                'booking_status' => $bookingStatus,
                'payment_status' => $advance >= $total ? 'paid' : ($advance > 0 ? 'partial' : 'unpaid'),
                'created_by' => auth()->id(),
            ]);

            $event->services()->sync($serviceSyncData);
        }

        $this->command->info('Banquet dummy data seeded successfully!');
        $this->command->info('  - Amenities: ' . HallAmenity::count());
        $this->command->info('  - Halls: ' . Hall::count());
        $this->command->info('  - Services: ' . EventService::count());
        $this->command->info('  - Events: ' . Event::count());
    }
}
