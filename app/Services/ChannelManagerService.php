<?php

namespace App\Services;

use App\Models\OtaChannel;
use App\Models\RoomTypeChannelMapping;
use App\Models\RatePlan;
use App\Models\Reservation;
use App\Models\RoomType;
use Carbon\Carbon;
use Exception;

class ChannelManagerService
{
    public function syncChannel(OtaChannel $channel): array
    {
        $result = ['reservations' => 0, 'rates' => 0, 'availability' => 0];

        $service = $this->getService($channel);

        if ($channel->sync_reservations) {
            $reservationResult = $service->pullReservations();
            $result['reservations'] = $reservationResult['processed'];
        }

        if ($channel->sync_rates) {
            $rates = $this->prepareRates($channel);
            if (!empty($rates)) {
                $service->pushRates($rates);
                $result['rates'] = count($rates);
            }
        }

        if ($channel->sync_availability) {
            $availability = $this->prepareAvailability($channel);
            if (!empty($availability)) {
                $service->pushAvailability($availability);
                $result['availability'] = count($availability);
            }
        }

        $channel->update(['last_synced_at' => Carbon::now()]);

        return $result;
    }

    private function getService(OtaChannel $channel)
    {
        return match($channel->provider) {
            'booking_com' => new BookingComService($channel),
            'expedia' => new ExpediaService($channel),
            'agoda' => new AgodaService($channel),
            'airbnb' => new AirbnbService($channel),
            'makemytrip' => new MakeMyTripService($channel),
            'goibibo' => new GoibiboService($channel),
            'trip_com' => new TripComService($channel),
            'hostelworld' => new HostelworldService($channel),
            default => throw new Exception("Unsupported OTA provider: {$channel->provider}"),
        };
    }

    private function prepareRates(OtaChannel $channel): array
    {
        $mappings = RoomTypeChannelMapping::where('ota_channel_id', $channel->id)
            ->where('sync_rates', true)
            ->where('status', 'active')
            ->get();

        $rates = [];
        $today = Carbon::today();
        $endDate = Carbon::today()->addDays(30);

        foreach ($mappings as $mapping) {
            $ratePlans = RatePlan::where('room_type_id', $mapping->room_type_id)
                ->where('hotel_id', $channel->hotel_id)
                ->where('effective_from', '<=', $endDate)
                ->where('effective_to', '>=', $today)
                ->get();

            foreach ($ratePlans as $plan) {
                $adjustedRate = $plan->rate_per_night * $mapping->rate_multiplier;

                $rates[] = [
                    'room_type' => $mapping->ota_room_type_id ?? $mapping->roomType->name,
                    'date' => $today->toDateString(),
                    'rate' => round($adjustedRate, 2),
                    'currency' => 'USD',
                    'min_stay' => $plan->min_stay ?? 1,
                    'max_stay' => $plan->max_stay ?? 30,
                ];
            }
        }

        return $rates;
    }

    private function prepareAvailability(OtaChannel $channel): array
    {
        $mappings = RoomTypeChannelMapping::where('ota_channel_id', $channel->id)
            ->where('sync_availability', true)
            ->where('status', 'active')
            ->get();

        $availability = [];
        $today = Carbon::today();

        foreach ($mappings as $mapping) {
            $totalRooms = \App\Models\Room::where('room_type_id', $mapping->room_type_id)
                ->where('hotel_id', $channel->hotel_id)
                ->count();

            $bookedRooms = Reservation::where('hotel_id', $channel->hotel_id)
                ->whereHas('rooms', function ($q) use ($mapping) {
                    $q->where('room_type_id', $mapping->room_type_id);
                })
                ->where('check_in_date', '<=', $today)
                ->where('check_out_date', '>', $today)
                ->whereNotIn('status', ['cancelled', 'no-show'])
                ->count();

            $available = $totalRooms - $bookedRooms;

            $availability[] = [
                'room_type' => $mapping->ota_room_type_id ?? $mapping->roomType->name,
                'date' => $today->toDateString(),
                'available_rooms' => max(0, $available),
            ];
        }

        return $availability;
    }
}
