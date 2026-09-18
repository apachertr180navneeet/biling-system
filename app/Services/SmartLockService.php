<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\SmartLockAccessCode;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SmartLockService
{
    public function generateAccessCode(Device $device, Reservation $reservation): ?SmartLockAccessCode
    {
        try {
            $pinCode = $this->generatePin();
            $validFrom = $reservation->actual_check_in ?? $reservation->check_in_date ?? now();
            $validUntil = $reservation->actual_check_out ?? $reservation->check_out_date ?? now()->addDays(1);

            $response = Http::timeout(10)->post("http://{$device->ip_address}:{$device->port}/api/access-code", [
                'api_key' => $device->api_key,
                'pin_code' => $pinCode,
                'valid_from' => Carbon::parse($validFrom)->toIso8601String(),
                'valid_until' => Carbon::parse($validUntil)->toIso8601String(),
                'guest_name' => $reservation->guest->full_name ?? '',
            ]);

            if ($response->successful()) {
                $accessCode = SmartLockAccessCode::create([
                    'device_id' => $device->id,
                    'reservation_id' => $reservation->id,
                    'guest_id' => $reservation->guest_id,
                    'pin_code' => $pinCode,
                    'access_type' => 'pin',
                    'valid_from' => $validFrom,
                    'valid_until' => $validUntil,
                    'is_active' => true,
                    'issued_by' => auth()->id(),
                ]);

                DeviceLog::create([
                    'device_id' => $device->id,
                    'event_type' => 'access_code_generated',
                    'user_id' => auth()->id(),
                    'reservation_id' => $reservation->id,
                    'data' => ['pin_code' => $pinCode, 'valid_until' => $validUntil],
                    'status' => 'success',
                    'message' => "Access code generated for {$reservation->guest->full_name ?? 'guest'}",
                ]);

                return $accessCode;
            }

            throw new Exception('Lock returned error: ' . $response->body());
        } catch (Exception $e) {
            Log::error("Smart lock generate access code failed for device {$device->id}: " . $e->getMessage());
            DeviceLog::create([
                'device_id' => $device->id,
                'event_type' => 'access_code_generated',
                'user_id' => auth()->id(),
                'reservation_id' => $reservation->id,
                'data' => ['error' => $e->getMessage()],
                'status' => 'failed',
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function revokeAccessCode(SmartLockAccessCode $code): bool
    {
        try {
            $device = $code->device;

            $response = Http::timeout(10)->post("http://{$device->ip_address}:{$device->port}/api/revoke-code", [
                'api_key' => $device->api_key,
                'pin_code' => $code->pin_code,
            ]);

            $code->update(['is_active' => false]);

            DeviceLog::create([
                'device_id' => $device->id,
                'event_type' => 'access_code_revoked',
                'user_id' => auth()->id(),
                'reservation_id' => $code->reservation_id,
                'data' => ['pin_code' => $code->pin_code],
                'status' => 'success',
                'message' => 'Access code revoked',
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("Smart lock revoke failed for code {$code->id}: " . $e->getMessage());
            return false;
        }
    }

    public function revokeAllForReservation(Reservation $reservation): void
    {
        $activeCodes = SmartLockAccessCode::where('reservation_id', $reservation->id)
            ->where('is_active', true)
            ->get();

        foreach ($activeCodes as $code) {
            $this->revokeAccessCode($code);
        }
    }

    public function lockDoor(Device $device): bool
    {
        try {
            $response = Http::timeout(10)->post("http://{$device->ip_address}:{$device->port}/api/lock", [
                'api_key' => $device->api_key,
            ]);

            if ($response->successful()) {
                DeviceLog::create([
                    'device_id' => $device->id,
                    'event_type' => 'door_lock',
                    'user_id' => auth()->id(),
                    'data' => ['action' => 'remote_lock'],
                    'status' => 'success',
                    'message' => 'Door locked remotely',
                ]);
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error("Smart lock lockDoor failed for device {$device->id}: " . $e->getMessage());
            return false;
        }
    }

    public function unlockDoor(Device $device): bool
    {
        try {
            $response = Http::timeout(10)->post("http://{$device->ip_address}:{$device->port}/api/unlock", [
                'api_key' => $device->api_key,
            ]);

            if ($response->successful()) {
                DeviceLog::create([
                    'device_id' => $device->id,
                    'event_type' => 'door_unlock',
                    'user_id' => auth()->id(),
                    'data' => ['action' => 'remote_unlock'],
                    'status' => 'success',
                    'message' => 'Door unlocked remotely',
                ]);
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error("Smart lock unlockDoor failed for device {$device->id}: " . $e->getMessage());
            return false;
        }
    }

    public function getDoorStatus(Device $device): ?string
    {
        try {
            $response = Http::timeout(5)->get("http://{$device->ip_address}:{$device->port}/api/status", [
                'api_key' => $device->api_key,
            ]);

            if ($response->successful()) {
                $device->update(['last_seen_at' => now()]);
                return $response->json('status', 'unknown');
            }

            return 'offline';
        } catch (Exception $e) {
            Log::error("Smart lock status check failed for device {$device->id}: " . $e->getMessage());
            return 'offline';
        }
    }

    private function generatePin(): string
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}
