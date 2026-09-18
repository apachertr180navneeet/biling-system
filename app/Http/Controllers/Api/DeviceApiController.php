<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\SmartLockAccessCode;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Exception;

class DeviceApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $apiKey = $request->header('X-Device-API-Key') ?? $request->input('api_key');
            if (!$apiKey) {
                return response()->json(['error' => 'API key required'], 401);
            }
            $device = Device::where('api_key', $apiKey)->first();
            if (!$device) {
                return response()->json(['error' => 'Invalid API key'], 401);
            }
            $request->merge(['_device' => $device]);
            $device->update(['last_seen_at' => now()]);
            return $next($request);
        });
    }

    public function heartbeat(Request $request, Device $device)
    {
        $device->update(['last_seen_at' => now(), 'status' => 'active']);

        return response()->json([
            'success' => true,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function pushBiometricLogs(Request $request, Device $device)
    {
        try {
            $request->validate([
                'logs' => 'required|array',
                'logs.*.employee_id' => 'required|integer',
                'logs.*.timestamp' => 'required|date',
                'logs.*.fingerprint_id' => 'nullable|integer',
            ]);

            $processed = 0;

            foreach ($request->logs as $log) {
                $employee = Employee::find($log['employee_id']);
                if (!$employee) continue;

                $checkTime = Carbon::parse($log['timestamp']);
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
                        'fingerprint_id' => $log['fingerprint_id'] ?? null,
                        'timestamp' => $log['timestamp'],
                    ],
                    'status' => 'success',
                    'message' => "Attendance recorded for {$employee->full_name}",
                ]);

                $processed++;
            }

            return response()->json(['success' => true, 'processed' => $processed]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function pushSmartLockEvent(Request $request, Device $device)
    {
        try {
            $request->validate([
                'event' => 'required|in:door_unlock,door_lock,door_forced',
                'timestamp' => 'required|date',
                'pin_code' => 'nullable|string',
            ]);

            $status = $request->event === 'door_forced' ? 'failed' : 'success';
            $message = match($request->event) {
                'door_unlock' => 'Door unlocked',
                'door_lock' => 'Door locked',
                'door_forced' => 'Forced entry attempt detected!',
                default => 'Unknown event',
            };

            $employeeId = null;
            $reservationId = null;

            if ($request->pin_code) {
                $accessCode = SmartLockAccessCode::where('device_id', $device->id)
                    ->where('pin_code', $request->pin_code)
                    ->where('is_active', true)
                    ->first();

                if ($accessCode && $accessCode->isValid()) {
                    $reservationId = $accessCode->reservation_id;
                } elseif ($request->event === 'door_unlock') {
                    $status = 'warning';
                    $message = 'Access code used but expired or inactive';
                }
            }

            DeviceLog::create([
                'device_id' => $device->id,
                'event_type' => $request->event,
                'reservation_id' => $reservationId,
                'data' => [
                    'pin_code' => $request->pin_code,
                    'timestamp' => $request->timestamp,
                ],
                'status' => $status,
                'message' => $message,
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function getSmartLockAccessCodes(Request $request, Device $device)
    {
        $codes = SmartLockAccessCode::where('device_id', $device->id)
            ->active()
            ->get()
            ->map(fn($code) => [
                'pin_code' => $code->pin_code,
                'valid_from' => $code->valid_from->toIso8601String(),
                'valid_until' => $code->valid_until->toIso8601String(),
                'guest_name' => $code->reservation?->guest->full_name ?? '',
            ]);

        return response()->json(['success' => true, 'access_codes' => $codes]);
    }

    public function submitPrintJob(Request $request, Device $device)
    {
        try {
            $request->validate([
                'type' => 'required|in:invoice,kot,receipt',
                'content' => 'required|string',
            ]);

            DeviceLog::create([
                'device_id' => $device->id,
                'event_type' => 'print_job',
                'data' => [
                    'type' => $request->type,
                    'content' => $request->content,
                ],
                'status' => 'success',
                'message' => ucfirst($request->type) . ' print job received',
            ]);

            return response()->json(['success' => true, 'message' => 'Print job accepted']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
