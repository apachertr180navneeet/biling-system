<?php

namespace App\Http\Controllers\Admin\Integrations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\SmartLockAccessCode;
use App\Models\Hotel;
use App\Models\Room;
use App\Services\BiometricService;
use App\Services\PrinterService;
use App\Services\SmartLockService;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class DeviceController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.integrations.devices.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = Device::with('hotel')->select('devices.*');

        if ($request->type_filter && $request->type_filter !== 'all') {
            $query->where('type', $request->type_filter);
        }

        return DataTables::of($query)
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($d) => $d->hotel?->name ?? '')
            ->addColumn('type_badge', function ($device) {
                $colors = ['biometric' => 'primary', 'printer' => 'success', 'smart_lock' => 'info'];
                $labels = ['biometric' => 'Biometric', 'printer' => 'Printer', 'smart_lock' => 'Smart Lock'];
                $color = $colors[$device->type] ?? 'secondary';
                $label = $labels[$device->type] ?? $device->type;
                return "<span class=\"badge bg-{$color}\">{$label}</span>";
            })
            ->addColumn('status_badge', function ($device) {
                $colors = ['active' => 'success', 'inactive' => 'secondary', 'maintenance' => 'warning', 'offline' => 'danger'];
                $color = $colors[$device->status] ?? 'secondary';
                return "<span class=\"badge bg-{$color}\">" . ucfirst($device->status) . "</span>";
            })
            ->addColumn('online_indicator', function ($device) {
                return $device->isOnline()
                    ? '<span class="badge bg-success"><i class="bx bxs-circle"></i> Online</span>'
                    : '<span class="badge bg-secondary"><i class="bx bx-circle"></i> Offline</span>';
            })
            ->addColumn('actions', function ($device) {
                $editUrl = route('admin.integrations.devices.edit', $device);
                $showUrl = route('admin.integrations.devices.show', $device);
                $deleteUrl = route('admin.integrations.devices.destroy', $device);
                return "
                    <div class='btn-group'>
                        <a href='{$showUrl}' class='btn btn-sm btn-info' title='View'><i class='bx bx-show'></i></a>
                        <a href='{$editUrl}' class='btn btn-sm btn-primary' title='Edit'><i class='bx bx-edit'></i></a>
                        <button class='btn btn-sm btn-danger btn-delete' data-url='{$deleteUrl}' title='Delete'><i class='bx bx-trash'></i></button>
                    </div>";
            })
            ->rawColumns(['type_badge', 'status_badge', 'online_indicator', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $device = null;
        $hotels = Hotel::where('status', 'active')->get();
        $rooms = Room::where('status', 'active')->get();
        return view('admin.integrations.devices.form', compact('device', 'hotels', 'rooms'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'type' => 'required|in:biometric,printer,smart_lock',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'serial_number' => 'required|string|max:255|unique:devices,serial_number',
                'ip_address' => 'nullable|ip',
                'port' => 'nullable|integer|min:1|max:65535',
                'api_key' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'room_id' => 'nullable|exists:rooms,id',
                'settings' => 'nullable|array',
            ]);

            Device::create($request->all());

            return redirect()->route('admin.integrations.devices.index')->with('success', 'Device added successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Device $device)
    {
        $device->load('hotel', 'room', 'logs.employee', 'logs.user', 'accessCodes.reservation.guest');
        $recentLogs = $device->logs()->latest()->limit(50)->get();
        $activeAccessCodes = $device->accessCodes()->active()->with('reservation.guest')->get();
        return view('admin.integrations.devices.show', compact('device', 'recentLogs', 'activeAccessCodes'));
    }

    public function edit(Device $device)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $rooms = Room::where('status', 'active')->get();
        return view('admin.integrations.devices.form', compact('device', 'hotels', 'rooms'));
    }

    public function update(Request $request, Device $device)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'name' => 'required|string|max:255',
                'type' => 'required|in:biometric,printer,smart_lock',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'serial_number' => 'required|string|max:255|unique:devices,serial_number,' . $device->id,
                'ip_address' => 'nullable|ip',
                'port' => 'nullable|integer|min:1|max:65535',
                'api_key' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'room_id' => 'nullable|exists:rooms,id',
                'settings' => 'nullable|array',
            ]);

            $device->update($request->all());

            return redirect()->route('admin.integrations.devices.index')->with('success', 'Device updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Device $device)
    {
        try {
            $device->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Device deleted!']);
            }
            return redirect()->route('admin.integrations.devices.index')->with('success', 'Device deleted!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Device $device)
    {
        try {
            $statuses = ['active', 'inactive', 'maintenance', 'offline'];
            $currentIndex = array_search($device->status, $statuses);
            $device->status = $statuses[($currentIndex + 1) % count($statuses)];
            $device->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $device->status, 'message' => 'Device status updated!']);
            }
            return redirect()->back()->with('success', 'Device status updated!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function testConnection(Request $request, Device $device)
    {
        try {
            $result = false;

            switch ($device->type) {
                case 'biometric':
                    $service = new BiometricService();
                    $result = $service->connect($device);
                    break;
                case 'printer':
                    $service = new PrinterService();
                    $result = $service->testPrint($device);
                    break;
                case 'smart_lock':
                    $service = new SmartLockService();
                    $status = $service->getDoorStatus($device);
                    $result = $status !== 'offline';
                    break;
            }

            if ($result) {
                $device->update(['last_seen_at' => now(), 'status' => 'active']);
                return response()->json(['success' => true, 'message' => 'Device connection successful!']);
            }

            $device->update(['status' => 'offline']);
            return response()->json(['success' => false, 'message' => 'Device connection failed!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function syncBiometric(Request $request, Device $device)
    {
        try {
            if ($device->type !== 'biometric') {
                throw new Exception('This device is not a biometric device.');
            }

            $service = new BiometricService();
            $result = $service->pullAttendanceLogs($device);

            $message = "Synced {$result['processed']} attendance records.";
            if (isset($result['error'])) {
                $message .= " Error: {$result['error']}";
            }

            return response()->json([
                'success' => $result['processed'] > 0,
                'message' => $message,
                'processed' => $result['processed'],
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function lockDoor(Request $request, Device $device)
    {
        try {
            if ($device->type !== 'smart_lock') {
                throw new Exception('This device is not a smart lock.');
            }

            $service = new SmartLockService();
            $result = $service->lockDoor($device);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Door locked successfully!' : 'Failed to lock door.',
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function unlockDoor(Request $request, Device $device)
    {
        try {
            if ($device->type !== 'smart_lock') {
                throw new Exception('This device is not a smart lock.');
            }

            $service = new SmartLockService();
            $result = $service->unlockDoor($device);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Door unlocked successfully!' : 'Failed to unlock door.',
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Device Logs
    public function logs()
    {
        return view('admin.integrations.logs.index');
    }

    public function logsData(Request $request)
    {
        $query = DeviceLog::with(['device', 'employee', 'user', 'reservation'])->select('device_logs.*');

        return DataTables::of($query)
            ->filterColumn('device_name', function ($query, $value) {
                $query->whereHas('device', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('device_name', fn($l) => $l->device?->name ?? '')
            ->addColumn('event_badge', function ($log) {
                $colors = [
                    'fingerprint_scan' => 'primary',
                    'print_job' => 'success',
                    'door_unlock' => 'info',
                    'door_lock' => 'info',
                    'door_forced' => 'danger',
                    'access_code_generated' => 'success',
                    'access_code_revoked' => 'warning',
                    'offline' => 'secondary',
                    'error' => 'danger',
                ];
                $color = $colors[$log->event_type] ?? 'secondary';
                $label = ucwords(str_replace('_', ' ', $log->event_type));
                return "<span class=\"badge bg-{$color}\">{$label}</span>";
            })
            ->addColumn('status_badge', function ($log) {
                $colors = ['success' => 'success', 'failed' => 'danger', 'warning' => 'warning'];
                $color = $colors[$log->status] ?? 'secondary';
                return "<span class=\"badge bg-{$color}\">" . ucfirst($log->status) . "</span>";
            })
            ->addColumn('employee_name', fn($l) => $l->employee?->full_name ?? '-')
            ->addColumn('user_name', fn($l) => $l->user?->name ?? '-')
            ->rawColumns(['event_badge', 'status_badge'])
            ->make(true);
    }

    // Smart Lock Access Codes
    public function accessCodes()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.integrations.access-codes.index', compact('hotels'));
    }

    public function accessCodesData(Request $request)
    {
        $query = SmartLockAccessCode::with(['device', 'reservation.guest', 'reservation.room'])
            ->select('smart_lock_access_codes.*');

        return DataTables::of($query)
            ->filterColumn('guest_name', function ($query, $value) {
                $query->whereHas('reservation.guest', function ($q) use ($value) {
                    $q->where('first_name', 'like', "%{$value}%")
                        ->orWhere('last_name', 'like', "%{$value}%");
                });
            })
            ->addColumn('guest_name', fn($ac) => $ac->reservation?->guest->full_name ?? '-')
            ->addColumn('room_number', fn($ac) => $ac->reservation?->room?->room_number ?? '-')
            ->addColumn('device_name', fn($ac) => $ac->device?->name ?? '')
            ->addColumn('valid_from_fmt', fn($ac) => $ac->valid_from?->format('d M Y, h:i A') ?? '')
            ->addColumn('valid_until_fmt', fn($ac) => $ac->valid_until?->format('d M Y, h:i A') ?? '')
            ->addColumn('status_badge', function ($ac) {
                if (!$ac->is_active) {
                    return '<span class="badge bg-secondary">Revoked</span>';
                }
                if ($ac->valid_until < now()) {
                    return '<span class="badge bg-warning">Expired</span>';
                }
                return '<span class="badge bg-success">Active</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }
}
