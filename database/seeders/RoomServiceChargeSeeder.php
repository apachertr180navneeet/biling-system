<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\RoomServiceCharge;
use App\Models\RestaurantOrder;
use App\Models\Reservation;
use App\Models\User;
use App\Helpers\Helper;
use Carbon\Carbon;

class RoomServiceChargeSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        $users = User::where('status', 'active')->get();
        $reservations = Reservation::whereIn('status', ['confirmed', 'checked-in'])->get();

        if ($reservations->isEmpty()) {
            $this->command->info('No reservations found. Creating sample room service charges with first reservation.');
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            $res = $reservations->random();
            $date = Carbon::now()->subDays($i);
            $totalAmount = rand(300, 1500);
            $taxAmount = round($totalAmount * 0.05, 2);

            $order = RestaurantOrder::create([
                'hotel_id' => $hotel->id,
                'order_number' => 'RS-' . $date->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'slug' => 'rs-' . $date->format('Ymd') . '-' . ($i + 1),
                'order_type' => 'room_service',
                'guest_name' => null,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'net_amount' => $totalAmount + $taxAmount,
                'payment_method' => 'room_charge',
                'payment_status' => 'pending',
                'order_status' => 'completed',
                'created_by' => $users->random()->id,
                'status' => 'active',
            ]);

            RoomServiceCharge::create([
                'hotel_id' => $hotel->id,
                'reservation_id' => $res->id,
                'restaurant_order_id' => $order->id,
                'charge_number' => 'RSC-' . $date->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'slug' => 'rsc-' . strtolower($order->order_number),
                'amount' => $order->total_amount,
                'tax_amount' => $order->tax_amount,
                'total_amount' => $order->net_amount,
                'posted_by' => $users->random()->id,
                'posted_at' => $date,
                'notes' => 'Room service charge for ' . $order->order_number,
                'charge_status' => 'posted',
                'status' => 'active',
            ]);
        }

        $this->command->info('Created 5 room service charges successfully!');
    }
}
