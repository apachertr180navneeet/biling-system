<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CleaningSchedule;
use App\Models\LaundryItem;
use App\Models\LaundryOrder;
use App\Models\RestaurantTable;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\RoomServiceCharge;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use App\Models\Reservation;
use App\Helpers\Helper;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        if (!$hotel) {
            $this->command->error('No active hotel found. Please seed hotels first.');
            return;
        }

        $rooms = Room::where('hotel_id', $hotel->id)->where('status', 'active')->get();
        if ($rooms->isEmpty()) {
            $this->command->error('No active rooms found for this hotel. Please seed rooms first.');
            return;
        }

        $users = User::where('status', 'active')->get();
        $reservations = Reservation::whereIn('status', ['confirmed', 'checked-in'])->get();

        // ── Cleaning Schedules ──
        $cleaningTypes = ['checkout', 'stay_over', 'deep_cleaning', 'turndown'];
        $priorities = ['high', 'medium', 'low'];
        $statuses = ['pending', 'in_progress', 'completed'];

        for ($i = 0; $i < 15; $i++) {
            $room = $rooms->random();
            $status = $statuses[array_rand($statuses)];
            $completedAt = $status === 'completed' ? Carbon::now()->subHours(rand(1, 8)) : null;

            CleaningSchedule::create([
                'hotel_id' => $hotel->id,
                'room_id' => $room->id,
                'cleaning_type' => $cleaningTypes[array_rand($cleaningTypes)],
                'scheduled_date' => Carbon::today()->addDays(rand(-1, 3)),
                'scheduled_time' => sprintf('%02d:%02d', rand(6, 18), [0, 15, 30, 45][array_rand([0, 15, 30, 45])]),
                'assigned_to' => $users->random()->id,
                'priority' => $priorities[array_rand($priorities)],
                'notes' => collect([
                    'Check under bed and sofa',
                    'Replace all towels',
                    'Guest requested extra pillows',
                    'VIP room - deep clean required',
                    'Heavy stain on carpet near window',
                    'Extra amenities needed',
                    'Checkout - full inspection required',
                    'Guest complained about dust',
                    null,
                ])->random(),
                'status' => $status,
                'completed_at' => $completedAt,
                'completed_by' => $status === 'completed' ? $users->random()->id : null,
            ]);
        }

        // ── Laundry Items ──
        $laundryItemsData = [
            ['name' => 'Bath Towel', 'item_type' => 'towel', 'quantity' => 200, 'unit' => 'pieces'],
            ['name' => 'Hand Towel', 'item_type' => 'towel', 'quantity' => 300, 'unit' => 'pieces'],
            ['name' => 'Face Towel', 'item_type' => 'towel', 'quantity' => 250, 'unit' => 'pieces'],
            ['name' => 'Pool Towel', 'item_type' => 'towel', 'quantity' => 80, 'unit' => 'pieces'],
            ['name' => 'Bath Mat', 'item_type' => 'linen', 'quantity' => 150, 'unit' => 'pieces'],
            ['name' => 'King Bed Sheet', 'item_type' => 'linen', 'quantity' => 120, 'unit' => 'pieces'],
            ['name' => 'Queen Bed Sheet', 'item_type' => 'linen', 'quantity' => 180, 'unit' => 'pieces'],
            ['name' => 'Twin Bed Sheet', 'item_type' => 'linen', 'quantity' => 100, 'unit' => 'pieces'],
            ['name' => 'Pillow Case (King)', 'item_type' => 'linen', 'quantity' => 240, 'unit' => 'pieces'],
            ['name' => 'Pillow Case (Queen)', 'item_type' => 'linen', 'quantity' => 360, 'unit' => 'pieces'],
            ['name' => 'Duvet Cover (King)', 'item_type' => 'linen', 'quantity' => 60, 'unit' => 'pieces'],
            ['name' => 'Duvet Cover (Queen)', 'item_type' => 'linen', 'quantity' => 90, 'unit' => 'pieces'],
            ['name' => 'Tablecloth (Round)', 'item_type' => 'linen', 'quantity' => 40, 'unit' => 'pieces'],
            ['name' => 'Napkin', 'item_type' => 'linen', 'quantity' => 500, 'unit' => 'pieces'],
            ['name' => 'Uniform - Housekeeping', 'item_type' => 'uniform', 'quantity' => 60, 'unit' => 'pieces'],
            ['name' => 'Uniform - Restaurant', 'item_type' => 'uniform', 'quantity' => 45, 'unit' => 'pieces'],
            ['name' => 'Chef Coat', 'item_type' => 'uniform', 'quantity' => 20, 'unit' => 'pieces'],
            ['name' => 'Room Service Apron', 'item_type' => 'uniform', 'quantity' => 30, 'unit' => 'pieces'],
            ['name' => 'Curtain Panel', 'item_type' => 'linen', 'quantity' => 80, 'unit' => 'pieces'],
            ['name' => 'Table Runner', 'item_type' => 'other', 'quantity' => 35, 'unit' => 'pieces'],
        ];

        $laundryItems = collect();
        foreach ($laundryItemsData as $item) {
            $laundryItems->push(LaundryItem::create([
                'hotel_id' => $hotel->id,
                'name' => $item['name'],
                'slug' => Helper::slug('laundry_items', $item['name']),
                'item_type' => $item['item_type'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'description' => 'Standard ' . strtolower($item['item_type']) . ' item for hotel operations',
                'status' => 'active',
            ]));
        }

        // ── Laundry Orders ──
        $vendors = ['FreshLaundry Co.', 'Hotel Linen Services', 'QuickWash Pro', 'Premium Laundry Ltd.'];
        $orderStatuses = ['pending', 'in_progress', 'completed'];

        for ($i = 0; $i < 8; $i++) {
            $orderDate = Carbon::now()->subDays(rand(0, 10));
            $status = $orderStatuses[array_rand($orderStatuses)];
            $totalItems = rand(20, 150);
            $selectedItems = $laundryItems->random(rand(3, 8));

            $order = LaundryOrder::create([
                'hotel_id' => $hotel->id,
                'order_number' => 'LO-' . $orderDate->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'slug' => 'lo-' . $orderDate->format('Ymd') . '-' . ($i + 1),
                'order_date' => $orderDate,
                'expected_return_date' => $orderDate->copy()->addDays(rand(2, 5)),
                'actual_return_date' => $status === 'completed' ? $orderDate->copy()->addDays(rand(2, 5)) : null,
                'vendor_name' => $vendors[array_rand($vendors)],
                'total_items' => $totalItems,
                'total_weight' => round($totalItems * rand(3, 8) / 10, 2),
                'total_cost' => round($totalItems * rand(15, 40) / 10, 2),
                'notes' => collect([
                    'Priority - restaurant linens needed for weekend event',
                    'Standard weekly laundry batch',
                    'Express delivery requested',
                    'Include stain treatment for tablecloths',
                    null,
                ])->random(),
                'order_status' => $status,
                'status' => 'active',
            ]);

            foreach ($selectedItems as $item) {
                $qty = rand(5, 40);
                $order->items()->attach($item->id, [
                    'quantity_sent' => $qty,
                    'quantity_received' => $status === 'completed' ? $qty - rand(0, 2) : null,
                    'damage_count' => $status === 'completed' ? rand(0, 1) : 0,
                    'notes' => null,
                ]);
            }
        }

        // ── Restaurant Tables ──
        $sections = ['Indoor', 'Outdoor', 'VIP', 'Bar Area', 'Private Dining'];
        $tableStatuses = ['available', 'occupied', 'reserved', 'maintenance'];

        $tableConfigs = [
            ['number' => 'T1', 'capacity' => 2, 'floor' => 1, 'section' => 'Indoor'],
            ['number' => 'T2', 'capacity' => 2, 'floor' => 1, 'section' => 'Indoor'],
            ['number' => 'T3', 'capacity' => 4, 'floor' => 1, 'section' => 'Indoor'],
            ['number' => 'T4', 'capacity' => 4, 'floor' => 1, 'section' => 'Indoor'],
            ['number' => 'T5', 'capacity' => 6, 'floor' => 1, 'section' => 'Indoor'],
            ['number' => 'T6', 'capacity' => 8, 'floor' => 1, 'section' => 'Indoor'],
            ['number' => 'T7', 'capacity' => 4, 'floor' => 1, 'section' => 'Window Side'],
            ['number' => 'T8', 'capacity' => 4, 'floor' => 1, 'section' => 'Window Side'],
            ['number' => 'O1', 'capacity' => 4, 'floor' => 0, 'section' => 'Outdoor'],
            ['number' => 'O2', 'capacity' => 6, 'floor' => 0, 'section' => 'Outdoor'],
            ['number' => 'O3', 'capacity' => 8, 'floor' => 0, 'section' => 'Outdoor'],
            ['number' => 'O4', 'capacity' => 4, 'floor' => 0, 'section' => 'Outdoor'],
            ['number' => 'VIP1', 'capacity' => 10, 'floor' => 2, 'section' => 'VIP'],
            ['number' => 'VIP2', 'capacity' => 12, 'floor' => 2, 'section' => 'VIP'],
            ['number' => 'B1', 'capacity' => 2, 'floor' => 0, 'section' => 'Bar Area'],
            ['number' => 'B2', 'capacity' => 4, 'floor' => 0, 'section' => 'Bar Area'],
            ['number' => 'P1', 'capacity' => 6, 'floor' => 2, 'section' => 'Private Dining'],
            ['number' => 'P2', 'capacity' => 8, 'floor' => 2, 'section' => 'Private Dining'],
        ];

        foreach ($tableConfigs as $cfg) {
            RestaurantTable::create([
                'hotel_id' => $hotel->id,
                'table_number' => $cfg['number'],
                'slug' => Helper::slug('restaurant_tables', $cfg['number']),
                'capacity' => $cfg['capacity'],
                'floor_number' => $cfg['floor'],
                'section' => $cfg['section'],
                'table_status' => $tableStatuses[array_rand($tableStatuses)],
                'status' => 'active',
            ]);
        }

        // ── Menu Items ──
        $menuItemsData = [
            // Appetizers
            ['name' => 'Spring Rolls', 'category' => 'appetizer', 'price' => 280, 'tax_rate' => 5, 'prep_time' => 10],
            ['name' => 'Chicken Wings', 'category' => 'appetizer', 'price' => 350, 'tax_rate' => 5, 'prep_time' => 15],
            ['name' => 'Bruschetta', 'category' => 'appetizer', 'price' => 220, 'tax_rate' => 5, 'prep_time' => 8],
            ['name' => 'Soup of the Day', 'category' => 'appetizer', 'price' => 180, 'tax_rate' => 5, 'prep_time' => 5],
            ['name' => 'Caesar Salad', 'category' => 'appetizer', 'price' => 250, 'tax_rate' => 5, 'prep_time' => 8],
            ['name' => 'Fish Fingers', 'category' => 'appetizer', 'price' => 320, 'tax_rate' => 5, 'prep_time' => 12],
            // Main Course
            ['name' => 'Grilled Chicken Breast', 'category' => 'main_course', 'price' => 550, 'tax_rate' => 5, 'prep_time' => 20],
            ['name' => 'Butter Chicken', 'category' => 'main_course', 'price' => 480, 'tax_rate' => 5, 'prep_time' => 25],
            ['name' => 'Paneer Tikka Masala', 'category' => 'main_course', 'price' => 420, 'tax_rate' => 5, 'prep_time' => 20],
            ['name' => 'Lamb Rogan Josh', 'category' => 'main_course', 'price' => 620, 'tax_rate' => 5, 'prep_time' => 30],
            ['name' => 'Fish & Chips', 'category' => 'main_course', 'price' => 450, 'tax_rate' => 5, 'prep_time' => 18],
            ['name' => 'Veg Biryani', 'category' => 'main_course', 'price' => 350, 'tax_rate' => 5, 'prep_time' => 20],
            ['name' => 'Chicken Biryani', 'category' => 'main_course', 'price' => 400, 'tax_rate' => 5, 'prep_time' => 22],
            ['name' => 'Dal Makhani', 'category' => 'main_course', 'price' => 320, 'tax_rate' => 5, 'prep_time' => 15],
            ['name' => 'Pasta Alfredo', 'category' => 'main_course', 'price' => 380, 'tax_rate' => 5, 'prep_time' => 15],
            ['name' => 'Grilled Salmon', 'category' => 'main_course', 'price' => 750, 'tax_rate' => 5, 'prep_time' => 25],
            // Desserts
            ['name' => 'Chocolate Brownie', 'category' => 'dessert', 'price' => 220, 'tax_rate' => 5, 'prep_time' => 5],
            ['name' => 'Cheesecake', 'category' => 'dessert', 'price' => 280, 'tax_rate' => 5, 'prep_time' => 5],
            ['name' => 'Gulab Jamun', 'category' => 'dessert', 'price' => 150, 'tax_rate' => 5, 'prep_time' => 5],
            ['name' => 'Ice Cream Sundae', 'category' => 'dessert', 'price' => 180, 'tax_rate' => 5, 'prep_time' => 5],
            ['name' => 'Tiramisu', 'category' => 'dessert', 'price' => 300, 'tax_rate' => 5, 'prep_time' => 5],
            // Beverages
            ['name' => 'Fresh Lime Soda', 'category' => 'beverage', 'price' => 120, 'tax_rate' => 5, 'prep_time' => 3],
            ['name' => 'Mango Lassi', 'category' => 'beverage', 'price' => 150, 'tax_rate' => 5, 'prep_time' => 5],
            ['name' => 'Espresso', 'category' => 'beverage', 'price' => 100, 'tax_rate' => 5, 'prep_time' => 3],
            ['name' => 'Cappuccino', 'category' => 'beverage', 'price' => 150, 'tax_rate' => 5, 'prep_time' => 5],
            ['name' => 'Orange Juice', 'category' => 'beverage', 'price' => 130, 'tax_rate' => 5, 'prep_time' => 3],
            ['name' => 'Bottled Water', 'category' => 'beverage', 'price' => 50, 'tax_rate' => 0, 'prep_time' => 1],
            ['name' => 'Masala Chai', 'category' => 'beverage', 'price' => 80, 'tax_rate' => 5, 'prep_time' => 5],
            // Specials
            ['name' => "Chef's Special Thali", 'category' => 'special', 'price' => 650, 'tax_rate' => 5, 'prep_time' => 30],
            ['name' => 'Weekend Brunch Platter', 'category' => 'special', 'price' => 850, 'tax_rate' => 5, 'prep_time' => 25],
        ];

        $menuItems = collect();
        foreach ($menuItemsData as $item) {
            $menuItems->push(RestaurantMenuItem::create([
                'hotel_id' => $hotel->id,
                'name' => $item['name'],
                'slug' => Helper::slug('restaurant_menu_items', $item['name']),
                'category' => $item['category'],
                'description' => 'Delicious ' . str_replace('_', ' ', $item['category']) . ' prepared by our expert chefs',
                'price' => $item['price'],
                'tax_rate' => $item['tax_rate'],
                'preparation_time' => $item['prep_time'],
                'is_available' => rand(0, 10) > 1,
                'status' => 'active',
            ]));
        }

        // ── Restaurant Orders ──
        $tables = RestaurantTable::where('hotel_id', $hotel->id)->get();
        $orderTypes = ['dine_in', 'takeaway', 'room_service'];
        $paymentMethods = ['cash', 'card', 'upi', 'room_charge'];
        $paymentStatuses = ['pending', 'paid', 'partially_paid'];
        $orderStatuses = ['pending', 'preparing', 'ready', 'served', 'completed', 'cancelled'];
        $guestNames = ['Mr. Rahul Sharma', 'Ms. Priya Patel', 'Dr. Amit Verma', 'Mrs. Neha Gupta', 'Mr. Arjun Singh', 'Ms. Kavita Joshi', 'Mr. Vikram Rao', 'Ms. Pooja Mehta'];

        for ($i = 0; $i < 20; $i++) {
            $orderDate = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 12));
            $orderType = $orderTypes[array_rand($orderTypes)];
            $status = $orderStatuses[array_rand($orderStatuses)];
            $payStatus = $status === 'completed' ? 'paid' : $paymentStatuses[array_rand($paymentStatuses)];
            $selectedItems = $menuItems->random(rand(2, 6));

            $totalAmount = 0;
            foreach ($selectedItems as $item) {
                $totalAmount += $item->price * rand(1, 3);
            }
            $taxAmount = round($totalAmount * 5 / 100, 2);
            $discount = rand(0, 1) ? round($totalAmount * rand(5, 15) / 100, 2) : 0;
            $netAmount = round($totalAmount + $taxAmount - $discount, 2);

            $tableId = null;
            if ($orderType === 'dine_in') {
                $tableId = $tables->random()->id;
            }

            $reservationId = null;
            if ($orderType === 'room_service' && $reservations->count()) {
                $reservationId = $reservations->random()->id;
            }

            $order = RestaurantOrder::create([
                'hotel_id' => $hotel->id,
                'order_number' => 'RO-' . $orderDate->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'slug' => 'ro-' . $orderDate->format('Ymd') . '-' . ($i + 1),
                'restaurant_table_id' => $tableId,
                'reservation_id' => $reservationId,
                'guest_name' => $orderType === 'room_service' ? null : $guestNames[array_rand($guestNames)],
                'order_type' => $orderType,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discount,
                'net_amount' => $netAmount,
                'payment_method' => $payStatus === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null,
                'payment_status' => $payStatus,
                'notes' => collect([
                    'No onion please',
                    'Extra spicy',
                    'Allergic to nuts',
                    'Well done',
                    'Room 405 - ring doorbell twice',
                    'Birthday celebration - need candle',
                    null,
                ])->random(),
                'order_status' => $status,
                'created_by' => $users->random()->id,
                'status' => 'active',
            ]);

            foreach ($selectedItems as $item) {
                $qty = rand(1, 3);
                $order->items()->create([
                    'restaurant_menu_item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $qty,
                    'special_instructions' => collect([
                        'Extra sauce',
                        'No ice',
                        'Medium rare',
                        null,
                    ])->random(),
                    'item_status' => $status === 'completed' ? 'served' : 'pending',
                ]);
            }

            // Update table status for dine-in orders
            if ($tableId && in_array($status, ['pending', 'preparing', 'ready', 'served'])) {
                RestaurantTable::where('id', $tableId)->update(['table_status' => 'occupied']);
            }
        }

        // ── Room Service Charges ──
        $roomServiceOrders = RestaurantOrder::where('order_type', 'room_service')
            ->whereIn('order_status', ['completed', 'served'])
            ->get();

        if ($reservations->count() && $roomServiceOrders->count()) {
            foreach ($roomServiceOrders->take(10) as $order) {
                $res = $reservations->random();
                RoomServiceCharge::create([
                    'hotel_id' => $hotel->id,
                    'reservation_id' => $res->id,
                    'restaurant_order_id' => $order->id,
                    'charge_number' => 'RSC-' . now()->format('Ymd') . str_pad($order->id, 3, '0', STR_PAD_LEFT),
                    'slug' => 'rsc-' . now()->format('Ymd') . '-' . strtolower($order->order_number),
                    'amount' => $order->total_amount,
                    'tax_amount' => $order->tax_amount,
                    'total_amount' => $order->net_amount,
                    'posted_by' => $users->random()->id,
                    'posted_at' => $order->created_at->addMinutes(rand(5, 30)),
                    'notes' => 'Charge for order ' . $order->order_number,
                    'charge_status' => 'posted',
                    'status' => 'active',
                ]);
            }
        }

        $this->command->info('Dummy data seeded successfully!');
        $this->command->info('  - Cleaning Schedules: 15');
        $this->command->info('  - Laundry Items: ' . count($laundryItemsData));
        $this->command->info('  - Laundry Orders: 8');
        $this->command->info('  - Restaurant Tables: ' . count($tableConfigs));
        $this->command->info('  - Menu Items: ' . count($menuItemsData));
        $this->command->info('  - Restaurant Orders: 20');
        $this->command->info('  - Room Service Charges: ' . min($roomServiceOrders->count(), 10));
    }
}
