<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\Room;

class CreateTestGuest extends Command
{
    protected $signature = 'test:create-guest';
    protected $description = 'Create test guest with active reservation';

    public function handle()
    {
        $slug = 'john-smith-' . time();
        $guest = Guest::firstOrCreate(
            ['email' => 'john@test.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'slug' => $slug,
                'phone' => '+1234567890',
                'nationality' => 'American',
                'id_type' => 'passport',
                'id_number' => 'AB1234567',
            ]
        );

        $existing = Reservation::where('guest_id', $guest->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked-in'])
            ->first();

        if ($existing) {
            $this->info("Active reservation exists: {$existing->reservation_number}");
            $this->printCredentials($guest, $existing);
            return 0;
        }

        $hotel = Hotel::first();
        $roomType = RoomType::first();
        $room = Room::first();

        $reservation = Reservation::create([
            'reservation_number' => 'RES-TEST001',
            'hotel_id' => $hotel->id,
            'guest_id' => $guest->id,
            'booking_source' => 'walk-in',
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'total_amount' => 599.97,
            'paid_amount' => 300.00,
            'discount_amount' => 0,
            'tax_amount' => 59.97,
            'status' => 'confirmed',
            'created_by' => 1,
        ]);

        if ($room) {
            ReservationRoom::create([
                'reservation_id' => $reservation->id,
                'room_id' => $room->id,
                'room_type_id' => $roomType->id,
                'check_in_date' => $reservation->check_in_date,
                'check_out_date' => $reservation->check_out_date,
                'rate_per_night' => 179.99,
                'total_amount' => 539.97,
                'status' => 'confirmed',
            ]);
        }

        $this->info("Test guest and reservation created!");
        $this->printCredentials($guest, $reservation);

        return 0;
    }

    private function printCredentials(Guest $guest, Reservation $reservation)
    {
        $this->newLine();
        $this->info("========== LOGIN CREDENTIALS ==========");
        $this->info("Reservation Number: {$reservation->reservation_number}");
        $this->info("Email: {$guest->email}");
        $this->newLine();
        $this->info("========== URLs ==========");
        $this->info("Guest Login:     http://localhost/guest/login");
        $this->info("Guest Dashboard: http://localhost/guest/dashboard");
        $this->info("Staff Dashboard: http://localhost/staff/dashboard");
        $this->newLine();
        $this->info("========== STAFF LOGIN ==========");
        $this->info("Admin Login:     http://localhost/admin/login");
    }
}
