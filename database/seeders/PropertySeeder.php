<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Wing;
use App\Models\RoomType;
use App\Models\BedType;
use App\Models\RoomStatus;
use App\Models\Amenity;
use App\Models\Room;
use App\Models\RatePlan;
use App\Helpers\Helper;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        if (!$hotel) {
            $this->command->error('No active hotel found.');
            return;
        }

        if (Room::where('hotel_id', $hotel->id)->exists()) {
            $this->command->info('Rooms already exist. Skipping.');
            return;
        }

        $building = Building::create([
            'hotel_id' => $hotel->id,
            'name' => 'Main Building',
            'slug' => Helper::slug('buildings', 'Main Building-' . $hotel->id),
            'description' => 'Main hotel building',
            'status' => 'active',
        ]);

        $floors = [];
        for ($i = 1; $i <= 4; $i++) {
            $floors[$i] = Floor::create([
                'building_id' => $building->id,
                'name' => "Floor $i",
                'slug' => Helper::slug('floors', "Floor $i-" . $hotel->id),
                'floor_number' => $i,
                'status' => 'active',
            ]);
        }

        $wing = Wing::create([
            'floor_id' => $floors[1]->id,
            'name' => 'East Wing',
            'slug' => Helper::slug('wings', 'East Wing-' . $hotel->id),
            'description' => 'East wing of the hotel',
            'status' => 'active',
        ]);

        $bedTypes = [];
        foreach (['Single Bed', 'Double Bed', 'King Bed', 'Twin Beds', 'Queen Bed'] as $name) {
            $bedTypes[$name] = BedType::create([
                'name' => $name,
                'slug' => Helper::slug('bed_types', $name),
                'description' => "Standard $name",
                'status' => 'active',
            ]);
        }

        $roomTypesData = [
            ['name' => 'Standard Room', 'base_rate' => 2500, 'max_occupancy' => 2],
            ['name' => 'Deluxe Room', 'base_rate' => 4000, 'max_occupancy' => 2],
            ['name' => 'Executive Suite', 'base_rate' => 6500, 'max_occupancy' => 3],
            ['name' => 'Family Suite', 'base_rate' => 8000, 'max_occupancy' => 4],
            ['name' => 'Presidential Suite', 'base_rate' => 15000, 'max_occupancy' => 2],
        ];

        $roomTypes = [];
        foreach ($roomTypesData as $rt) {
            $roomTypes[$rt['name']] = RoomType::firstOrCreate(
                ['name' => $rt['name']],
                [
                    'slug' => Helper::slug('room_types', $rt['name']),
                    'base_rate' => $rt['base_rate'],
                    'max_occupancy' => $rt['max_occupancy'],
                    'description' => 'Comfortable ' . strtolower($rt['name']) . ' with modern amenities',
                    'status' => 'active',
                ]
            );
        }

        $roomStatuses = [];
        foreach (['Vacant Clean' => '#28a745', 'Vacant Dirty' => '#ffc107', 'Occupied' => '#dc3545', 'Out of Order' => '#6c757d'] as $name => $color) {
            $roomStatuses[$name] = RoomStatus::firstOrCreate(
                ['name' => $name],
                ['slug' => Helper::slug('room_statuses', $name), 'color' => $color, 'status' => 'active']
            );
        }

        $amenities = [];
        foreach (['WiFi', 'AC', 'Mini Bar', 'Room Service', 'Balcony', 'Sea View', 'Bathtub', 'TV'] as $name) {
            $amenities[$name] = Amenity::firstOrCreate(
                ['name' => $name],
                ['slug' => Helper::slug('amenities', $name), 'description' => "Room amenity: $name", 'status' => 'active']
            );
        }

        $roomConfig = [
            1 => [
                ['number' => '101', 'type' => 'Standard Room', 'bed' => 'Double Bed'],
                ['number' => '102', 'type' => 'Standard Room', 'bed' => 'Double Bed'],
                ['number' => '103', 'type' => 'Standard Room', 'bed' => 'Twin Beds'],
                ['number' => '104', 'type' => 'Deluxe Room', 'bed' => 'King Bed'],
                ['number' => '105', 'type' => 'Deluxe Room', 'bed' => 'Queen Bed'],
            ],
            2 => [
                ['number' => '201', 'type' => 'Standard Room', 'bed' => 'Double Bed'],
                ['number' => '202', 'type' => 'Deluxe Room', 'bed' => 'King Bed'],
                ['number' => '203', 'type' => 'Deluxe Room', 'bed' => 'Queen Bed'],
                ['number' => '204', 'type' => 'Executive Suite', 'bed' => 'King Bed'],
                ['number' => '205', 'type' => 'Standard Room', 'bed' => 'Twin Beds'],
            ],
            3 => [
                ['number' => '301', 'type' => 'Deluxe Room', 'bed' => 'King Bed'],
                ['number' => '302', 'type' => 'Executive Suite', 'bed' => 'King Bed'],
                ['number' => '303', 'type' => 'Family Suite', 'bed' => 'King Bed'],
                ['number' => '304', 'type' => 'Deluxe Room', 'bed' => 'Queen Bed'],
                ['number' => '305', 'type' => 'Standard Room', 'bed' => 'Double Bed'],
            ],
            4 => [
                ['number' => '401', 'type' => 'Executive Suite', 'bed' => 'King Bed'],
                ['number' => '402', 'type' => 'Family Suite', 'bed' => 'King Bed'],
                ['number' => '403', 'type' => 'Presidential Suite', 'bed' => 'King Bed'],
                ['number' => '404', 'type' => 'Deluxe Room', 'bed' => 'Queen Bed'],
                ['number' => '405', 'type' => 'Standard Room', 'bed' => 'Double Bed'],
            ],
        ];

        $roomCount = 0;
        foreach ($roomConfig as $floorNum => $rooms) {
            foreach ($rooms as $rc) {
                $room = Room::create([
                    'hotel_id' => $hotel->id,
                    'building_id' => $building->id,
                    'floor_id' => $floors[$floorNum]->id,
                    'wing_id' => $wing->id,
                    'room_type_id' => $roomTypes[$rc['type']]->id,
                    'bed_type_id' => $bedTypes[$rc['bed']]->id,
                    'room_status_id' => $roomStatuses['Vacant Clean']->id,
                    'room_number' => $rc['number'],
                    'slug' => Helper::slug('rooms', $rc['number'] . '-' . $hotel->id),
                    'floor_label' => "Floor $floorNum",
                    'description' => $roomTypes[$rc['type']]->description,
                    'status' => 'active',
                ]);

                $amenitySubset = array_slice(array_values($amenities), 0, rand(4, 6));
                foreach ($amenitySubset as $amenity) {
                    $room->amenities()->attach($amenity->id);
                }

                $roomCount++;
            }
        }

        $ratePlans = [
            ['name' => 'Standard Rate', 'room_type' => 'Standard Room', 'rate' => 2500],
            ['name' => 'Standard Rate', 'room_type' => 'Deluxe Room', 'rate' => 4000],
            ['name' => 'Standard Rate', 'room_type' => 'Executive Suite', 'rate' => 6500],
            ['name' => 'Standard Rate', 'room_type' => 'Family Suite', 'rate' => 8000],
            ['name' => 'Standard Rate', 'room_type' => 'Presidential Suite', 'rate' => 15000],
        ];

        foreach ($ratePlans as $rp) {
            RatePlan::create([
                'hotel_id' => $hotel->id,
                'room_type_id' => $roomTypes[$rp['room_type']]->id,
                'name' => $rp['name'],
                'rate_per_night' => $rp['rate'],
                'effective_from' => now()->subYear(),
                'effective_to' => now()->addYear(),
                'status' => 'active',
            ]);
        }

        $this->command->info("Property seeded for {$hotel->name}:");
        $this->command->info("  - Rooms: $roomCount across 4 floors");
        $this->command->info("  - Room Types: " . count($roomTypesData));
        $this->command->info("  - Rate Plans: " . count($ratePlans));
    }
}
