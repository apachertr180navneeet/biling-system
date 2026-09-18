<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceAsset;
use App\Models\MaintenanceAmc;
use App\Models\MaintenanceWorkOrder;
use App\Models\Vendor;
use App\Models\Employee;
use App\Models\Hotel;

class MaintenanceDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::first();
        if (!$hotel) {
            $this->command->warn('No hotel found. Skipping maintenance dummy data.');
            return;
        }
        $hotelId = $hotel->id;

        // Create Assets
        $assetsData = [
            ['asset_code' => 'AST-0001', 'name' => 'Central AC Unit - Lobby', 'category' => 'HVAC', 'brand' => 'Daikin', 'model' => 'VRV IV', 'serial_number' => 'DK-VRV-2024-001', 'purchase_date' => '2024-03-15', 'purchase_cost' => 450000.00, 'warranty_expiry_date' => '2027-03-15', 'location' => 'Lobby', 'status' => 'active', 'description' => 'Main lobby central air conditioning unit'],
            ['asset_code' => 'AST-0002', 'name' => 'Elevator A', 'category' => 'Elevator', 'brand' => 'OTIS', 'model' => 'GeN2 Comfort', 'serial_number' => 'OT-GN2-2023-105', 'purchase_date' => '2023-01-10', 'purchase_cost' => 1200000.00, 'warranty_expiry_date' => '2026-01-10', 'location' => 'Building A', 'status' => 'active', 'description' => 'Passenger elevator serving floors G-10'],
            ['asset_code' => 'AST-0003', 'name' => 'Diesel Generator 500KVA', 'category' => 'Generator', 'brand' => 'Cummins', 'model' => 'C500D5', 'serial_number' => 'CM-500-2024-089', 'purchase_date' => '2024-06-20', 'purchase_cost' => 800000.00, 'warranty_expiry_date' => '2026-06-20', 'location' => 'Basement', 'status' => 'active', 'description' => 'Backup power generator for the entire property'],
            ['asset_code' => 'AST-0004', 'name' => 'Commercial Dishwasher', 'category' => 'Kitchen', 'brand' => 'Winterhalter', 'model' => 'PT-XL', 'serial_number' => 'WH-PTX-2024-045', 'purchase_date' => '2024-02-01', 'purchase_cost' => 350000.00, 'warranty_expiry_date' => '2026-02-01', 'location' => 'Kitchen', 'status' => 'active', 'description' => 'High-capacity conveyor dishwasher for restaurant'],
            ['asset_code' => 'AST-0005', 'name' => 'Fire Alarm Panel', 'category' => 'Fire Safety', 'brand' => 'Honeywell', 'model' => 'Morley-IAS', 'serial_number' => 'HW-FA-2023-200', 'purchase_date' => '2023-08-15', 'purchase_cost' => 180000.00, 'warranty_expiry_date' => '2025-08-15', 'location' => 'Security Room', 'status' => 'active', 'description' => 'Centralized fire alarm control panel'],
            ['asset_code' => 'AST-0006', 'name' => 'Water Pump Motor', 'category' => 'Plumbing', 'brand' => 'Grundfos', 'model' => 'CR 32-4', 'serial_number' => 'GF-CR32-2024-012', 'purchase_date' => '2024-04-10', 'purchase_cost' => 95000.00, 'warranty_expiry_date' => '2026-04-10', 'location' => 'Pump Room', 'status' => 'under_maintenance', 'description' => 'Main water supply pump motor'],
            ['asset_code' => 'AST-0007', 'name' => 'Conference Room Projector', 'category' => 'IT Hardware', 'brand' => 'Epson', 'model' => 'EB-2265U', 'serial_number' => 'EP-2265-2024-033', 'purchase_date' => '2024-01-20', 'purchase_cost' => 120000.00, 'warranty_expiry_date' => '2027-01-20', 'location' => 'Conference Room A', 'status' => 'active', 'description' => 'Full HD laser projector for conference room'],
            ['asset_code' => 'AST-0008', 'name' => 'Banquet Hall Chandelier', 'category' => 'Furniture', 'brand' => 'Crystal Palace', 'model' => 'Grand 48', 'serial_number' => 'CP-G48-2023-007', 'purchase_date' => '2023-12-01', 'purchase_cost' => 250000.00, 'warranty_expiry_date' => null, 'location' => 'Banquet Hall', 'status' => 'active', 'description' => 'Crystal chandelier, 48 arms, main banquet hall'],
        ];

        $assets = [];
        foreach ($assetsData as $data) {
            $data['hotel_id'] = $hotelId;
            $assets[] = MaintenanceAsset::create($data);
        }

        // Create AMC contracts
        $vendor = Vendor::where('status', 'active')->first();
        $vendorId = $vendor?->id;

        $amcsData = [
            ['asset_index' => 0, 'contract_number' => 'AMC-2024-001', 'start_date' => '2024-04-01', 'end_date' => '2025-03-31', 'cost' => 45000.00, 'contact_person' => 'Rajesh Kumar', 'contact_phone' => '9876543210', 'status' => 'active', 'description' => 'Comprehensive AMC for HVAC system including quarterly servicing'],
            ['asset_index' => 1, 'contract_number' => 'AMC-2024-002', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'cost' => 120000.00, 'contact_person' => 'Vikram Singh', 'contact_phone' => '9876543211', 'status' => 'active', 'description' => 'Annual elevator maintenance contract with bi-monthly inspections'],
            ['asset_index' => 2, 'contract_number' => 'AMC-2024-003', 'start_date' => '2024-07-01', 'end_date' => '2025-06-30', 'cost' => 60000.00, 'contact_person' => 'Suresh Mehta', 'contact_phone' => '9876543212', 'status' => 'active', 'description' => 'Generator AMC including fuel system maintenance and load testing'],
            ['asset_index' => 4, 'contract_number' => 'AMC-2023-005', 'start_date' => '2023-09-01', 'end_date' => '2024-08-31', 'cost' => 35000.00, 'contact_person' => 'Amit Patel', 'contact_phone' => '9876543214', 'status' => 'expired', 'description' => 'Fire alarm system maintenance and annual recertification'],
        ];

        foreach ($amcsData as $data) {
            MaintenanceAmc::create([
                'hotel_id' => $hotelId,
                'asset_id' => $assets[$data['asset_index']]->id,
                'vendor_id' => $vendorId,
                'contract_number' => $data['contract_number'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'cost' => $data['cost'],
                'contact_person' => $data['contact_person'],
                'contact_phone' => $data['contact_phone'],
                'status' => $data['status'],
                'description' => $data['description'],
            ]);
        }

        // Create Work Orders
        $employee = Employee::active()->first();
        $employeeId = $employee?->id;

        $workOrdersData = [
            ['work_order_number' => 'WO-2024-001', 'asset_index' => 5, 'title' => 'Water Pump Motor Bearing Replacement', 'type' => 'breakdown', 'priority' => 'high', 'schedule_date' => '2024-07-10', 'completion_date' => null, 'cost' => 15000.00, 'status' => 'in_progress', 'description' => 'Pump motor making unusual noise. Bearings need replacement.'],
            ['work_order_number' => 'WO-2024-002', 'asset_index' => 0, 'title' => 'Quarterly AC Filter Cleaning', 'type' => 'preventive', 'priority' => 'medium', 'schedule_date' => '2024-07-15', 'completion_date' => null, 'cost' => 5000.00, 'status' => 'pending', 'description' => 'Routine quarterly filter cleaning and gas pressure check.'],
            ['work_order_number' => 'WO-2024-003', 'asset_index' => 1, 'title' => 'Elevator Door Sensor Calibration', 'type' => 'preventive', 'priority' => 'medium', 'schedule_date' => '2024-07-05', 'completion_date' => '2024-07-05', 'cost' => 8000.00, 'status' => 'completed', 'description' => 'Bi-monthly door sensor alignment and calibration.'],
            ['work_order_number' => 'WO-2024-004', 'asset_index' => 2, 'title' => 'Generator Load Test', 'type' => 'preventive', 'priority' => 'low', 'schedule_date' => '2024-07-20', 'completion_date' => null, 'cost' => 3000.00, 'status' => 'pending', 'description' => 'Monthly load test and fuel level check.'],
            ['work_order_number' => 'WO-2024-005', 'asset_index' => 3, 'title' => 'Dishwasher Spray Arm Replacement', 'type' => 'breakdown', 'priority' => 'high', 'schedule_date' => '2024-07-08', 'completion_date' => '2024-07-09', 'cost' => 22000.00, 'status' => 'completed', 'description' => 'Lower spray arm cracked. Replaced with OEM part.'],
            ['work_order_number' => 'WO-2024-006', 'asset_index' => null, 'title' => 'Pool Area Tile Repair', 'type' => 'breakdown', 'priority' => 'medium', 'schedule_date' => '2024-07-12', 'completion_date' => null, 'cost' => 12000.00, 'status' => 'pending', 'description' => 'Cracked tiles near pool edge need replacement for guest safety.'],
        ];

        foreach ($workOrdersData as $data) {
            MaintenanceWorkOrder::create([
                'hotel_id' => $hotelId,
                'work_order_number' => $data['work_order_number'],
                'asset_id' => $data['asset_index'] !== null ? $assets[$data['asset_index']]->id : null,
                'title' => $data['title'],
                'type' => $data['type'],
                'priority' => $data['priority'],
                'assigned_employee_id' => $employeeId,
                'schedule_date' => $data['schedule_date'],
                'completion_date' => $data['completion_date'],
                'cost' => $data['cost'],
                'description' => $data['description'],
                'status' => $data['status'],
            ]);
        }

        $this->command->info('Maintenance dummy data seeded successfully: ' . count($assets) . ' assets, ' . count($amcsData) . ' AMCs, ' . count($workOrdersData) . ' work orders.');
    }
}
