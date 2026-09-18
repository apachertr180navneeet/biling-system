<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\ReservationPayment;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\User;
use App\Models\RoomType;
use App\Models\RatePlan;
use App\Helpers\Helper;
use Carbon\Carbon;

class CheckOutDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::where('status', 'active')->first();
        if (!$hotel) {
            $this->command->error('No active hotel found.');
            return;
        }

        $users = User::where('status', 'active')->get();
        if ($users->isEmpty()) {
            $this->command->error('No active users found.');
            return;
        }

        $rooms = Room::where('hotel_id', $hotel->id)->get();
        if ($rooms->isEmpty()) {
            $this->command->error('No rooms found.');
            return;
        }

        // We can check out some of the existing checked-in reservations
        $checkedInReservations = Reservation::where('status', 'checked-in')->get();

        $dirtyStatus = RoomStatus::where('slug', 'dirty')->first();

        // 1. Process existing check-ins to make them checked-out
        foreach ($checkedInReservations->take(3) as $index => $reservation) {
            $checkIn = CheckIn::where('reservation_id', $reservation->id)->first();
            if (!$checkIn) {
                continue;
            }

            // Calculate dates
            $checkInTime = Carbon::parse($checkIn->arrival_time ?? $reservation->check_in_date);
            $checkOutTime = $checkInTime->copy()->addDays(rand(1, 4))->addHours(rand(2, 6));

            $totalCharges = $reservation->total_amount;
            $damageCharges = rand(0, 5) === 0 ? rand(500, 2000) : 0;
            $finalBillAmount = $totalCharges;
            $totalPayments = $finalBillAmount + $damageCharges;
            $balanceDue = 0;

            $roomCondition = $damageCharges > 0 ? 'damaged' : 'good';
            $damageNotes = $damageCharges > 0 ? 'Stained carpet or broken glassware.' : null;

            $feedbackRating = rand(3, 5);
            $feedbackNotes = collect([
                'Excellent service and stay!',
                'Very comfortable room and polite staff.',
                'Overall great experience, will visit again.',
                'Good stay but room service was slightly slow.',
                'Wonderful hospitality.'
            ])->random();

            // Create CheckOut record
            CheckOut::create([
                'reservation_id' => $reservation->id,
                'hotel_id' => $hotel->id,
                'room_id' => $checkIn->room_id,
                'check_out_time' => $checkOutTime,
                'final_bill_amount' => $finalBillAmount,
                'total_charges' => $totalCharges,
                'total_payments' => $totalPayments,
                'balance_due' => $balanceDue,
                'room_condition' => $roomCondition,
                'damage_notes' => $damageNotes,
                'damage_charges' => $damageCharges,
                'feedback_rating' => $feedbackRating,
                'feedback_notes' => $feedbackNotes,
                'checked_out_by' => $users->random()->id,
                'status' => 'active',
            ]);

            // Update Reservation
            $reservation->update([
                'status' => 'checked-out',
                'actual_check_out' => $checkOutTime,
                'paid_amount' => $totalPayments,
            ]);

            // Update ReservationRoom status to checked-out
            ReservationRoom::where('reservation_id', $reservation->id)->update(['status' => 'checked-out']);

            // Update Room status to dirty
            if ($checkIn->room_id && $dirtyStatus) {
                Room::where('id', $checkIn->room_id)->update(['room_status_id' => $dirtyStatus->id]);
            }
        }

        // 2. Create some new checked-out records from scratch so they are always available even if no check-ins exist
        $firstNames = ['John', 'Jane', 'Robert', 'Emily', 'Michael', 'Sarah', 'David', 'Jessica'];
        $lastNames = ['Smith', 'Doe', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis'];

        for ($i = 0; $i < 5; $i++) {
            $fName = $firstNames[array_rand($firstNames)];
            $lName = $lastNames[array_rand($lastNames)];
            $fullName = "{$fName} {$lName}";

            $guest = Guest::create([
                'first_name' => $fName,
                'last_name' => $lName,
                'slug' => Helper::slug('guests', $fullName . '-' . uniqid()),
                'email' => strtolower($fName . '.' . $lName . '@example.com'),
                'phone' => '98' . rand(10000000, 99999999),
                'id_type' => 'passport',
                'id_number' => 'PP' . rand(100000, 999999),
                'status' => 'active',
            ]);

            $checkInDate = Carbon::now()->subDays(rand(5, 15));
            $checkOutDate = $checkInDate->copy()->addDays(rand(2, 5));
            $checkOutTime = $checkOutDate->copy()->addHours(rand(10, 14));

            $room = $rooms->random();
            $roomType = RoomType::find($room->room_type_id) ?? RoomType::first();
            $ratePlan = RatePlan::where('room_type_id', $roomType->id)->first() ?? RatePlan::first();

            $ratePerNight = $roomType->base_fare ?? 1500;
            $nights = $checkInDate->diffInDays($checkOutDate);
            $totalAmount = $ratePerNight * $nights;
            $taxAmount = round($totalAmount * 0.12, 2);
            $totalWithTax = $totalAmount + $taxAmount;

            $reservationNumber = Reservation::generateNumber();
            $reservation = Reservation::create([
                'reservation_number' => $reservationNumber,
                'hotel_id' => $hotel->id,
                'guest_id' => $guest->id,
                'booking_source' => 'online',
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'actual_check_in' => $checkInDate->copy()->addHours(rand(12, 15)),
                'actual_check_out' => $checkOutTime,
                'adults' => rand(1, 2),
                'children' => rand(0, 1),
                'total_amount' => $totalWithTax,
                'paid_amount' => $totalWithTax,
                'discount_amount' => 0,
                'tax_amount' => $taxAmount,
                'status' => 'checked-out',
                'created_by' => $users->random()->id,
                'record_status' => 'active',
            ]);

            ReservationRoom::create([
                'reservation_id' => $reservation->id,
                'room_id' => $room->id,
                'room_type_id' => $roomType->id,
                'rate_plan_id' => $ratePlan?->id,
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'rate_per_night' => $ratePerNight,
                'total_amount' => $totalWithTax,
                'status' => 'checked-out',
            ]);

            ReservationPayment::create([
                'reservation_id' => $reservation->id,
                'reference_number' => 'PAY-' . strtoupper(uniqid()),
                'amount' => $totalWithTax,
                'payment_method' => 'card',
                'status' => 'completed',
                'payment_date' => $checkInDate,
            ]);

            CheckIn::create([
                'reservation_id' => $reservation->id,
                'hotel_id' => $hotel->id,
                'room_id' => $room->id,
                'key_card_numbers' => 'KC-' . rand(100, 999),
                'id_verified' => true,
                'id_document_type' => 'passport',
                'id_document_number' => $guest->id_number,
                'arrival_time' => $reservation->actual_check_in,
                'checked_in_by' => $users->random()->id,
                'status' => 'active',
            ]);

            $damageCharges = rand(0, 10) === 0 ? rand(500, 1500) : 0;
            $finalBill = $totalWithTax + $damageCharges;

            CheckOut::create([
                'reservation_id' => $reservation->id,
                'hotel_id' => $hotel->id,
                'room_id' => $room->id,
                'check_out_time' => $checkOutTime,
                'final_bill_amount' => $finalBill,
                'total_charges' => $totalWithTax + $damageCharges,
                'total_payments' => $finalBill,
                'balance_due' => 0,
                'room_condition' => $damageCharges > 0 ? 'damaged' : 'good',
                'damage_notes' => $damageCharges > 0 ? 'Minor damage to towel rack.' : null,
                'damage_charges' => $damageCharges,
                'feedback_rating' => rand(4, 5),
                'feedback_notes' => 'Everything was smooth and pleasant.',
                'checked_out_by' => $users->random()->id,
                'status' => 'active',
            ]);
        }

        $this->command->info('Check Out dummy data seeded successfully!');
    }
}
