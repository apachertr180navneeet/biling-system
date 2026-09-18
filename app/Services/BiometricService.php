<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BiometricService
{
    public function connect(Device $device): bool
    {
        try {
            $response = Http::timeout(5)->get("http://{$device->ip_address}:{$device->port}/api/ping", [
                'api_key' => $device->api_key,
            ]);

            if ($response->successful()) {
                $device->update(['last_seen_at' => now()]);
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::error("Biometric connect failed for device {$device->id}: " . $e->getMessage());
            return false;
        }
    }

    public function pullAttendanceLogs(Device $device, ?Carbon $from = null, ?Carbon $to = null): array
    {
        try {
            $from = $from ?? now()->startOfDay();
            $to = $to ?? now();

            $response = Http::timeout(30)->get("http://{$device->ip_address}:{$device->port}/api/attendance", [
                'api_key' => $device->api_key,
                'from' => $from->toIso8601String(),
                'to' => $to->toIso8601String(),
            ]);

            if ($response->failed()) {
                throw new Exception('Device returned error: ' . $response->body());
            }

            $logs = $response->json('logs', []);
            $processed = 0;

            foreach ($logs as $log) {
                $this->processAttendanceLog($device, $log);
                $processed++;
            }

            $device->update(['last_seen_at' => now()]);

            return ['processed' => $processed, 'total' => count($logs)];
        } catch (Exception $e) {
            Log::error("Biometric pull failed for device {$device->id}: " . $e->getMessage());
            return ['processed' => 0, 'total' => 0, 'error' => $e->getMessage()];
        }
    }

    public function syncAttendanceToDevice(Employee $employee, Device $device): bool
    {
        try {
            $response = Http::timeout(10)->post("http://{$device->ip_address}:{$device->port}/api/enroll", [
                'api_key' => $device->api_key,
                'user_id' => $employee->id,
                'name' => $employee->full_name,
                'employee_id' => $employee->employee_id,
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error("Biometric sync failed for employee {$employee->id}: " . $e->getMessage());
            return false;
        }
    }

    public function getEnrolledUsers(Device $device): array
    {
        try {
            $response = Http::timeout(10)->get("http://{$device->ip_address}:{$device->port}/api/users", [
                'api_key' => $device->api_key,
            ]);

            return $response->successful() ? $response->json('users', []) : [];
        } catch (Exception $e) {
            Log::error("Biometric getUsers failed for device {$device->id}: " . $e->getMessage());
            return [];
        }
    }

    private function processAttendanceLog(Device $device, array $log): void
    {
        $employeeId = $log['employee_id'] ?? null;
        $timestamp = $log['timestamp'] ?? null;
        $fingerprintId = $log['fingerprint_id'] ?? null;

        if (!$employeeId || !$timestamp) {
            return;
        }

        $employee = Employee::where('id', $employeeId)->first();
        if (!$employee) {
            return;
        }

        $checkTime = Carbon::parse($timestamp);
        $date = $checkTime->toDateString();
        $time = $checkTime->format('H:i:s');

        $existing = EmployeeAttendance::where('employee_id', $employee->id)
            ->where('date', $date)
            ->first();

        if ($existing) {
            if (!$existing->check_out) {
                $existing->update(['check_out' => $time]);
            }
        } else {
            EmployeeAttendance::create([
                'employee_id' => $employee->id,
                'date' => $date,
                'check_in' => $time,
                'status' => 'present',
            ]);
        }

        DeviceLog::create([
            'device_id' => $device->id,
            'event_type' => 'fingerprint_scan',
            'employee_id' => $employee->id,
            'data' => [
                'fingerprint_id' => $fingerprintId,
                'timestamp' => $timestamp,
            ],
            'status' => 'success',
            'message' => "Attendance recorded for {$employee->full_name}",
        ]);
    }
}
