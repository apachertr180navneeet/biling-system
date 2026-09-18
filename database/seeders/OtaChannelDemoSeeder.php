<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OtaChannel;
use App\Models\RoomTypeChannelMapping;

class OtaChannelDemoSeeder extends Seeder
{
    public function run(): void
    {
        $hotelId = 1;

        $providers = [
            [
                'name' => 'Booking.com',
                'provider' => 'booking_com',
                'api_key' => 'demo_booking_api_key_12345',
                'api_secret' => 'demo_booking_secret_67890',
                'property_id_on_ota' => 'BKG-HML-001',
                'endpoint_url' => 'https://www.booking.com/xml/demo',
            ],
            [
                'name' => 'Expedia',
                'provider' => 'expedia',
                'api_key' => 'demo_expedia_api_key_abcde',
                'api_secret' => 'demo_expedia_secret_fghij',
                'property_id_on_ota' => 'EXP-HML-002',
                'endpoint_url' => 'https://api.expedia.com/demo',
            ],
            [
                'name' => 'Agoda',
                'provider' => 'agoda',
                'api_key' => 'demo_agoda_api_key_11111',
                'api_secret' => 'demo_agoda_secret_22222',
                'property_id_on_ota' => 'AGD-HML-003',
                'endpoint_url' => 'https://api.agoda.com/demo',
            ],
            [
                'name' => 'Airbnb',
                'provider' => 'airbnb',
                'api_key' => 'demo_airbnb_api_key_33333',
                'api_secret' => 'demo_airbnb_secret_44444',
                'property_id_on_ota' => 'ABB-HML-004',
                'endpoint_url' => 'https://api.airbnb.com/v2/demo',
            ],
            [
                'name' => 'MakeMyTrip',
                'provider' => 'makemytrip',
                'api_key' => 'demo_mmt_api_key_55555',
                'api_secret' => 'demo_mmt_secret_66666',
                'property_id_on_ota' => 'MMT-HML-005',
                'endpoint_url' => 'https://api.makemytrip.com/demo',
            ],
            [
                'name' => 'Goibibo',
                'provider' => 'goibibo',
                'api_key' => 'demo_goibibo_api_key_77777',
                'api_secret' => 'demo_goibibo_secret_88888',
                'property_id_on_ota' => 'GIB-HML-006',
                'endpoint_url' => 'https://api.goibibo.com/demo',
            ],
            [
                'name' => 'Trip.com',
                'provider' => 'trip_com',
                'api_key' => 'demo_trip_api_key_99999',
                'api_secret' => 'demo_trip_secret_00000',
                'property_id_on_ota' => 'TRP-HML-007',
                'endpoint_url' => 'https://api.trip.com/demo',
            ],
            [
                'name' => 'Hostelworld',
                'provider' => 'hostelworld',
                'api_key' => 'demo_hostelworld_api_key_a1b2c',
                'api_secret' => 'demo_hostelworld_secret_d3e4f',
                'property_id_on_ota' => 'HSW-HML-008',
                'endpoint_url' => 'https://api.hostelworld.com/demo',
            ],
        ];

        $roomTypeMap = [
            1 => ['ota_id' => 'STD', 'ota_name' => 'Standard Room'],
            2 => ['ota_id' => 'DLX', 'ota_name' => 'Deluxe Room'],
            3 => ['ota_id' => 'EXE', 'ota_name' => 'Executive Suite'],
            4 => ['ota_id' => 'FAM', 'ota_name' => 'Family Suite'],
            5 => ['ota_id' => 'PRE', 'ota_name' => 'Presidential Suite'],
        ];

        foreach ($providers as $providerData) {
            $channel = OtaChannel::updateOrCreate(
                ['hotel_id' => $hotelId, 'provider' => $providerData['provider']],
                [
                    'name' => $providerData['name'],
                    'api_key' => $providerData['api_key'],
                    'api_secret' => $providerData['api_secret'],
                    'property_id_on_ota' => $providerData['property_id_on_ota'],
                    'endpoint_url' => $providerData['endpoint_url'],
                    'sync_rates' => true,
                    'sync_availability' => true,
                    'sync_reservations' => true,
                    'auto_sync' => false,
                    'status' => 'active',
                ]
            );

            foreach ($roomTypeMap as $roomTypeId => $roomData) {
                RoomTypeChannelMapping::updateOrCreate(
                    ['ota_channel_id' => $channel->id, 'room_type_id' => $roomTypeId],
                    [
                        'ota_room_type_id' => $roomData['ota_id'],
                        'ota_room_name' => $roomData['ota_name'],
                        'rate_multiplier' => 1.00,
                        'sync_rates' => true,
                        'sync_availability' => true,
                        'status' => 'active',
                    ]
                );
            }

            $this->command->info("Created channel: {$providerData['name']} with 5 room mappings");
        }
    }
}
