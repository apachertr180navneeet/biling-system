<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            [
                'hotel_id' => 1,
                'name' => 'Main Lobby Fingerprint Scanner',
                'type' => 'biometric',
                'brand' => 'ZKTeco',
                'model' => 'uFace 800',
                'serial_number' => 'ZKT-2024-001',
                'ip_address' => '192.168.1.101',
                'port' => 8080,
                'api_key' => 'zkteco-api-key-001',
                'location' => 'Main Lobby',
                'status' => 'active',
            ],
            [
                'hotel_id' => 1,
                'name' => 'Back Office Fingerprint Scanner',
                'type' => 'biometric',
                'brand' => 'eSSL',
                'model' => 'X990',
                'serial_number' => 'ESSL-2024-002',
                'ip_address' => '192.168.1.102',
                'port' => 8080,
                'api_key' => 'essl-api-key-002',
                'location' => 'Back Office',
                'status' => 'active',
            ],
            [
                'hotel_id' => 1,
                'name' => 'Front Desk Receipt Printer',
                'type' => 'printer',
                'brand' => 'Epson',
                'model' => 'TM-T88VI',
                'serial_number' => 'EPS-2024-003',
                'ip_address' => '192.168.1.201',
                'port' => 9100,
                'api_key' => 'epson-api-key-003',
                'location' => 'Front Desk',
                'status' => 'active',
            ],
            [
                'hotel_id' => 1,
                'name' => 'Restaurant KOT Printer',
                'type' => 'printer',
                'brand' => 'Epson',
                'model' => 'TM-T88VI',
                'serial_number' => 'EPS-2024-004',
                'ip_address' => '192.168.1.202',
                'port' => 9100,
                'api_key' => 'epson-api-key-004',
                'location' => 'Restaurant Kitchen',
                'status' => 'active',
            ],
            [
                'hotel_id' => 1,
                'name' => 'Room 101 Smart Lock',
                'type' => 'smart_lock',
                'brand' => 'Onity',
                'model' => 'HT Series',
                'serial_number' => 'ONT-2024-005',
                'ip_address' => '192.168.1.301',
                'port' => 8443,
                'api_key' => 'onity-api-key-005',
                'location' => 'Room 101',
                'room_id' => null,
                'status' => 'active',
            ],
            [
                'hotel_id' => 1,
                'name' => 'Room 102 Smart Lock',
                'type' => 'smart_lock',
                'brand' => 'Onity',
                'model' => 'HT Series',
                'serial_number' => 'ONT-2024-006',
                'ip_address' => '192.168.1.302',
                'port' => 8443,
                'api_key' => 'onity-api-key-006',
                'location' => 'Room 102',
                'room_id' => null,
                'status' => 'active',
            ],
            [
                'hotel_id' => 1,
                'name' => 'Room 103 Smart Lock',
                'type' => 'smart_lock',
                'brand' => 'Onity',
                'model' => 'HT Series',
                'serial_number' => 'ONT-2024-007',
                'ip_address' => '192.168.1.303',
                'port' => 8443,
                'api_key' => 'onity-api-key-007',
                'location' => 'Room 103',
                'room_id' => null,
                'status' => 'maintenance',
            ],
        ];

        foreach ($devices as $data) {
            Device::updateOrCreate(
                ['serial_number' => $data['serial_number']],
                $data
            );
        }
    }
}
