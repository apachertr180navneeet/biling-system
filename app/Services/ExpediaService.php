<?php

namespace App\Services;

use App\Models\OtaChannel;
use App\Models\OtaSyncLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ExpediaService
{
    private OtaChannel $channel;

    public function __construct(OtaChannel $channel)
    {
        $this->channel = $channel;
    }

    public function pullReservations(): array
    {
        $log = $this->createSyncLog('inbound', 'pull_reservations');

        try {
            $response = $this->makeRequest('GET', '/itineraries', [
                'hotel_id' => $this->channel->property_id_on_ota,
            ]);

            $this->updateSyncLog($log, $response, 'success');

            return $this->processReservations($response['itineraries'] ?? []);
        } catch (Exception $e) {
            $this->updateSyncLog($log, null, 'failed', $e->getMessage());
            throw $e;
        }
    }

    public function pushAvailability(array $availability): array
    {
        $log = $this->createSyncLog('outbound', 'push_availability');

        try {
            $response = $this->makeRequest('PUT', '/availability', [
                'hotel_id' => $this->channel->property_id_on_ota,
                'room_types' => $availability,
            ]);

            $this->updateSyncLog($log, $response, 'success');

            return $response;
        } catch (Exception $e) {
            $this->updateSyncLog($log, null, 'failed', $e->getMessage());
            throw $e;
        }
    }

    public function pushRates(array $rates): array
    {
        $log = $this->createSyncLog('outbound', 'push_rates');

        try {
            $response = $this->makeRequest('PUT', '/rate_plan', [
                'hotel_id' => $this->channel->property_id_on_ota,
                'rate_plans' => $rates,
            ]);

            $this->updateSyncLog($log, $response, 'success');

            return $response;
        } catch (Exception $e) {
            $this->updateSyncLog($log, null, 'failed', $e->getMessage());
            throw $e;
        }
    }

    public function cancelReservation(string $otaReservationId): array
    {
        $log = $this->createSyncLog('outbound', 'cancel_reservation');

        try {
            $response = $this->makeRequest('PUT', "/itineraries/{$otaReservationId}/cancel");

            $this->updateSyncLog($log, $response, 'success');

            return $response;
        } catch (Exception $e) {
            $this->updateSyncLog($log, null, 'failed', $e->getMessage());
            throw $e;
        }
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->channel->endpoint_url . $endpoint;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->channel->api_key,
            'Content-Type' => 'application/json',
            'Expedia-Hotel-ID' => $this->channel->property_id_on_ota,
        ])->timeout(30)->send($method, $url, $data);

        if ($response->failed()) {
            throw new Exception("Expedia API error: {$response->status()} - {$response->body()}");
        }

        return $response->json();
    }

    private function processReservations(array $itineraries): array
    {
        $processed = 0;

        foreach ($itineraries as $itinerary) {
            $existing = \App\Models\Reservation::where('ota_reservation_id', $itinerary['itinerary_id'])->first();

            if (!$existing) {
                $processed++;
            }
        }

        return ['processed' => $processed, 'total' => count($itineraries)];
    }

    private function createSyncLog(string $direction, string $action): OtaSyncLog
    {
        return OtaSyncLog::create([
            'ota_channel_id' => $this->channel->id,
            'direction' => $direction,
            'action' => $action,
            'status' => 'pending',
        ]);
    }

    private function updateSyncLog(OtaSyncLog $log, ?array $response, string $status, ?string $error = null): void
    {
        $log->update([
            'response_payload' => $response,
            'status' => $status,
            'error_message' => $error,
        ]);
    }
}
