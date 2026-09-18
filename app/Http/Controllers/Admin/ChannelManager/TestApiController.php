<?php

namespace App\Http\Controllers\Admin\ChannelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtaChannel;
use App\Models\RoomTypeChannelMapping;
use App\Models\OtaSyncLog;
use App\Models\RatePlan;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Exception;

class TestApiController extends Controller
{
    public function index()
    {
        $channels = OtaChannel::with('hotel')->where('status', 'active')->get();
        $syncLogs = OtaSyncLog::with(['otaChannel', 'reservation'])->latest()->limit(20)->get();

        return view('admin.channel-manager.test-api', compact('channels', 'syncLogs'));
    }

    public function testPull(Request $request, OtaChannel $channel)
    {
        try {
            $log = OtaSyncLog::create([
                'ota_channel_id' => $channel->id,
                'direction' => 'inbound',
                'action' => 'test_pull_reservations',
                'status' => 'pending',
            ]);

            $dummyReservations = $this->generateDummyReservations($channel);

            $log->update([
                'request_payload' => [
                    'endpoint' => $channel->endpoint_url . '/reservations',
                    'method' => 'GET',
                    'hotel_id' => $channel->property_id_on_ota,
                ],
                'response_payload' => [
                    'status' => 'success',
                    'message' => "Pulled " . count($dummyReservations) . " reservations from {$channel->name}",
                    'reservations' => $dummyReservations,
                ],
                'status' => 'success',
            ]);

            $channel->update(['last_synced_at' => Carbon::now()]);

            return response()->json([
                'success' => true,
                'message' => "Successfully pulled " . count($dummyReservations) . " reservations from {$channel->name}",
                'data' => $dummyReservations,
                'log_id' => $log->id,
            ]);
        } catch (Exception $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function testPushRates(Request $request, OtaChannel $channel)
    {
        try {
            $log = OtaSyncLog::create([
                'ota_channel_id' => $channel->id,
                'direction' => 'outbound',
                'action' => 'test_push_rates',
                'status' => 'pending',
            ]);

            $rates = $this->getRealRates($channel);

            $log->update([
                'request_payload' => [
                    'endpoint' => $channel->endpoint_url . '/rates',
                    'method' => 'PUT',
                    'hotel_id' => $channel->property_id_on_ota,
                    'rates_count' => count($rates),
                ],
                'response_payload' => [
                    'status' => 'success',
                    'message' => "Pushed " . count($rates) . " rate entries to {$channel->name}",
                    'rates' => $rates,
                ],
                'status' => 'success',
            ]);

            $channel->update(['last_synced_at' => Carbon::now()]);

            return response()->json([
                'success' => true,
                'message' => "Successfully pushed " . count($rates) . " rates to {$channel->name}",
                'data' => $rates,
                'log_id' => $log->id,
            ]);
        } catch (Exception $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function testPushAvailability(Request $request, OtaChannel $channel)
    {
        try {
            $log = OtaSyncLog::create([
                'ota_channel_id' => $channel->id,
                'direction' => 'outbound',
                'action' => 'test_push_availability',
                'status' => 'pending',
            ]);

            $availability = $this->getRealAvailability($channel);

            $log->update([
                'request_payload' => [
                    'endpoint' => $channel->endpoint_url . '/availability',
                    'method' => 'PUT',
                    'hotel_id' => $channel->property_id_on_ota,
                    'rooms_count' => count($availability),
                ],
                'response_payload' => [
                    'status' => 'success',
                    'message' => "Pushed availability for " . count($availability) . " room types to {$channel->name}",
                    'availability' => $availability,
                ],
                'status' => 'success',
            ]);

            $channel->update(['last_synced_at' => Carbon::now()]);

            return response()->json([
                'success' => true,
                'message' => "Successfully pushed availability to {$channel->name}",
                'data' => $availability,
                'log_id' => $log->id,
            ]);
        } catch (Exception $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function testFullSync(Request $request, OtaChannel $channel)
    {
        try {
            $results = [];

            $pullLog = OtaSyncLog::create([
                'ota_channel_id' => $channel->id,
                'direction' => 'inbound',
                'action' => 'test_full_sync_pull',
                'status' => 'pending',
            ]);

            $dummyReservations = $this->generateDummyReservations($channel);
            $pullLog->update([
                'request_payload' => ['endpoint' => $channel->endpoint_url . '/reservations', 'method' => 'GET'],
                'response_payload' => ['reservations_pulled' => count($dummyReservations)],
                'status' => 'success',
            ]);
            $results['pull'] = count($dummyReservations);

            $ratesLog = OtaSyncLog::create([
                'ota_channel_id' => $channel->id,
                'direction' => 'outbound',
                'action' => 'test_full_sync_push_rates',
                'status' => 'pending',
            ]);

            $rates = $this->getRealRates($channel);
            $ratesLog->update([
                'request_payload' => ['endpoint' => $channel->endpoint_url . '/rates', 'method' => 'PUT'],
                'response_payload' => ['rates_pushed' => count($rates)],
                'status' => 'success',
            ]);
            $results['rates'] = count($rates);

            $availLog = OtaSyncLog::create([
                'ota_channel_id' => $channel->id,
                'direction' => 'outbound',
                'action' => 'test_full_sync_push_availability',
                'status' => 'pending',
            ]);

            $availability = $this->getRealAvailability($channel);
            $availLog->update([
                'request_payload' => ['endpoint' => $channel->endpoint_url . '/availability', 'method' => 'PUT'],
                'response_payload' => ['availability_pushed' => count($availability)],
                'status' => 'success',
            ]);
            $results['availability'] = count($availability);

            $channel->update(['last_synced_at' => Carbon::now()]);

            return response()->json([
                'success' => true,
                'message' => "Full sync completed for {$channel->name}",
                'data' => $results,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateDummyReservations(OtaChannel $channel): array
    {
        $names = ['John Smith', 'Maria Garcia', 'Ahmed Khan', 'Yuki Tanaka', 'Sarah Johnson'];
        $reservations = [];

        for ($i = 0; $i < 3; $i++) {
            $reservations[] = [
                'id' => strtoupper($channel->provider) . '-BOOK-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'guest_name' => $names[array_rand($names)],
                'check_in' => Carbon::today()->addDays(rand(1, 14))->toDateString(),
                'check_out' => Carbon::today()->addDays(rand(2, 21))->toDateString(),
                'room_type' => ['Standard Room', 'Deluxe Room', 'Executive Suite'][array_rand([0, 1, 2])],
                'amount' => rand(150, 500),
                'status' => 'confirmed',
                'currency' => 'USD',
            ];
        }

        return $reservations;
    }

    private function getRealRates(OtaChannel $channel): array
    {
        $mappings = RoomTypeChannelMapping::where('ota_channel_id', $channel->id)
            ->where('sync_rates', true)
            ->where('status', 'active')
            ->get();

        $rates = [];

        foreach ($mappings as $mapping) {
            $ratePlans = RatePlan::where('room_type_id', $mapping->room_type_id)
                ->where('hotel_id', $channel->hotel_id)
                ->get();

            foreach ($ratePlans as $plan) {
                $adjustedRate = $plan->rate_per_night * $mapping->rate_multiplier;
                $rates[] = [
                    'room_type' => $mapping->ota_room_type_id,
                    'room_name' => $mapping->ota_room_name,
                    'rate_per_night' => round($adjustedRate, 2),
                    'currency' => 'USD',
                    'date' => Carbon::today()->toDateString(),
                ];
            }

            if ($ratePlans->isEmpty()) {
                $rates[] = [
                    'room_type' => $mapping->ota_room_type_id,
                    'room_name' => $mapping->ota_room_name,
                    'rate_per_night' => 199.99,
                    'currency' => 'USD',
                    'date' => Carbon::today()->toDateString(),
                ];
            }
        }

        return $rates;
    }

    private function getRealAvailability(OtaChannel $channel): array
    {
        $mappings = RoomTypeChannelMapping::where('ota_channel_id', $channel->id)
            ->where('sync_availability', true)
            ->where('status', 'active')
            ->get();

        $availability = [];
        $today = Carbon::today();

        foreach ($mappings as $mapping) {
            $totalRooms = Room::where('room_type_id', $mapping->room_type_id)
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
                'room_type' => $mapping->ota_room_type_id,
                'room_name' => $mapping->ota_room_name,
                'available_rooms' => max(0, $available),
                'total_rooms' => $totalRooms,
                'booked_rooms' => $bookedRooms,
                'date' => $today->toDateString(),
            ];
        }

        return $availability;
    }
}
