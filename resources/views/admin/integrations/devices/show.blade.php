@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-chip"></i></div>
            <div>
                <h4 class="m-page-title">{{ $device->name }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Integrations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.integrations.devices.index') }}">Devices</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $device->name }}</li>
                </ul>
            </div>
        </div>
        <div>
            <button class="btn btn-warning btn-test-connection" data-url="{{ route('admin.integrations.devices.test-connection', $device) }}"><i class="bx bx-link"></i> Test Connection</button>
            <a href="{{ route('admin.integrations.devices.edit', $device) }}" class="btn btn-primary"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.integrations.devices.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Device Information</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Type</label>
                            <div class="fw-bold">
                                @if($device->type == 'biometric')<span class="badge bg-primary">Biometric</span>
                                @elseif($device->type == 'printer')<span class="badge bg-success">Printer</span>
                                @else<span class="badge bg-info">Smart Lock</span>@endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status</label>
                            <div class="fw-bold">
                                @if($device->status == 'active')<span class="badge bg-success">Active</span>
                                @elseif($device->status == 'inactive')<span class="badge bg-secondary">Inactive</span>
                                @elseif($device->status == 'maintenance')<span class="badge bg-warning">Maintenance</span>
                                @else<span class="badge bg-danger">Offline</span>@endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Brand</label>
                            <div class="fw-bold">{{ $device->brand ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Model</label>
                            <div class="fw-bold">{{ $device->model ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Serial Number</label>
                            <div class="fw-bold">{{ $device->serial_number }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Hotel</label>
                            <div class="fw-bold">{{ $device->hotel?->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Location</label>
                            <div class="fw-bold">{{ $device->location ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Room</label>
                            <div class="fw-bold">{{ $device->room?->room_number ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">IP Address</label>
                            <div class="fw-bold">{{ $device->ip_address ?? '-' }}{{ $device->port ? ":{$device->port}" : '' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Last Seen</label>
                            <div class="fw-bold">{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Online Status</label>
                            <div class="fw-bold">
                                @if($device->isOnline())
                                <span class="badge bg-success"><i class="bx bxs-circle"></i> Online</span>
                                @else
                                <span class="badge bg-secondary"><i class="bx bx-circle"></i> Offline</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Smart Lock Actions --}}
            @if($device->type == 'smart_lock')
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Lock Controls</h5></div>
                <div class="card-body">
                    <div class="btn-group">
                        <button class="btn btn-success btn-lock" data-url="{{ route('admin.integrations.devices.lock', $device) }}"><i class="bx bx-lock"></i> Lock Door</button>
                        <button class="btn btn-warning btn-unlock" data-url="{{ route('admin.integrations.devices.unlock', $device) }}"><i class="bx bx-lock-open"></i> Unlock Door</button>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Active Access Codes</h5></div>
                <div class="card-body">
                    @if($activeAccessCodes->isEmpty())
                        <p class="text-muted">No active access codes.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead><tr><th>Guest</th><th>Room</th><th>PIN</th><th>Type</th><th>Valid From</th><th>Valid Until</th><th>Status</th></tr></thead>
                            <tbody>
                            @foreach($activeAccessCodes as $code)
                            <tr>
                                <td>{{ $code->reservation?->guest->full_name ?? '-' }}</td>
                                <td>{{ $code->reservation?->room?->room_number ?? '-' }}</td>
                                <td><code>{{ $code->pin_code }}</code></td>
                                <td>{{ strtoupper($code->access_type) }}</td>
                                <td>{{ $code->valid_from?->format('d M Y, h:i A') }}</td>
                                <td>{{ $code->valid_until?->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($code->isValid())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Expired</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Biometric Sync --}}
            @if($device->type == 'biometric')
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Biometric Sync</h5></div>
                <div class="card-body">
                    <button class="btn btn-primary btn-sync-biometric" data-url="{{ route('admin.integrations.devices.sync', $device) }}"><i class="bx bx-sync"></i> Sync Attendance Logs</button>
                    <p class="text-muted mt-2">Pull latest attendance records from this biometric device and create attendance entries.</p>
                </div>
            </div>
            @endif

            {{-- Device Logs --}}
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Recent Activity Logs</h5></div>
                <div class="card-body">
                    @if($recentLogs->isEmpty())
                        <p class="text-muted">No logs yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead><tr><th>Event</th><th>Status</th><th>User</th><th>Employee</th><th>Message</th><th>Time</th></tr></thead>
                            <tbody>
                            @foreach($recentLogs as $log)
                            <tr>
                                <td><span class="badge bg-{{ match($log->event_type) { 'fingerprint_scan' => 'primary', 'print_job' => 'success', 'door_unlock', 'door_lock' => 'info', 'door_forced' => 'danger', default => 'secondary' } }}">{{ ucwords(str_replace('_', ' ', $log->event_type)) }}</span></td>
                                <td><span class="badge bg-{{ $log->status == 'success' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">{{ ucfirst($log->status) }}</span></td>
                                <td>{{ $log->user?->name ?? '-' }}</td>
                                <td>{{ $log->employee?->full_name ?? '-' }}</td>
                                <td>{{ $log->message ?? '-' }}</td>
                                <td>{{ $log->created_at?->format('d M Y, h:i A') }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($device->type == 'biometric')
                        <i class="bx bx-fingerprint text-primary" style="font-size: 4rem;"></i>
                    @elseif($device->type == 'printer')
                        <i class="bx bx-printer text-success" style="font-size: 4rem;"></i>
                    @else
                        <i class="bx bx-lock text-info" style="font-size: 4rem;"></i>
                    @endif
                    <h5 class="mt-3">{{ $device->name }}</h5>
                    <p class="text-muted">{{ ucfirst(str_replace('_', ' ', $device->type)) }}</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-test-connection" data-url="{{ route('admin.integrations.devices.test-connection', $device) }}">
                            <i class="bx bx-link"></i> Test Connection
                        </button>
                        <a href="{{ route('admin.integrations.devices.edit', $device) }}" class="btn btn-outline-primary">
                            <i class="bx bx-edit"></i> Edit Device
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

$('.btn-test-connection').on('click', function() {
    var btn = $(this);
    var url = btn.data('url');
    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Testing...');
    $.post(url, {}, function(res) {
        Swal.fire({ icon: res.success ? 'success' : 'error', title: res.message, timer: 3000 });
    }).fail(function(xhr) {
        Swal.fire({ icon: 'error', title: xhr.responseJSON?.message || 'Connection failed!' });
    }).always(function() {
        btn.prop('disabled', false).html('<i class="bx bx-link"></i> Test Connection');
    });
});

$('.btn-lock').on('click', function() {
    var url = $(this).data('url');
    Swal.fire({ title: 'Lock Door?', text: 'Are you sure you want to lock this door?', icon: 'question', showCancelButton: true, confirmButtonColor: '#3085d6' }).then((result) => {
        if (result.isConfirmed) {
            $.post(url, {}, function(res) {
                Swal.fire({ icon: res.success ? 'success' : 'error', title: res.message, timer: 3000 });
            }).fail(function(xhr) {
                Swal.fire({ icon: 'error', title: 'Failed!' });
            });
        }
    });
});

$('.btn-unlock').on('click', function() {
    var url = $(this).data('url');
    Swal.fire({ title: 'Unlock Door?', text: 'This is an admin override. Are you sure?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ffc107' }).then((result) => {
        if (result.isConfirmed) {
            $.post(url, {}, function(res) {
                Swal.fire({ icon: res.success ? 'success' : 'error', title: res.message, timer: 3000 });
            }).fail(function(xhr) {
                Swal.fire({ icon: 'error', title: 'Failed!' });
            });
        }
    });
});

$('.btn-sync-biometric').on('click', function() {
    var btn = $(this);
    var url = btn.data('url');
    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Syncing...');
    $.post(url, {}, function(res) {
        Swal.fire({ icon: res.success ? 'success' : 'warning', title: res.message, timer: 5000 }).then(() => { location.reload(); });
    }).fail(function(xhr) {
        Swal.fire({ icon: 'error', title: xhr.responseJSON?.message || 'Sync failed!' });
    }).always(function() {
        btn.prop('disabled', false).html('<i class="bx bx-sync"></i> Sync Attendance Logs');
    });
});
</script>
@endsection
