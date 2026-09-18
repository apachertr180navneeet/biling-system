@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-chip"></i></div>
            <div>
                <h4 class="m-page-title">{{ $device ? 'Edit Device' : 'Add Device' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Integrations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.integrations.devices.index') }}">Devices</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $device ? 'Edit' : 'Add' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.integrations.devices.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ $device ? route('admin.integrations.devices.update', $device) : route('admin.integrations.devices.store') }}" method="POST">
                @csrf
                @if($device) @method('PUT') @endif

                <div class="m-section-divider">Basic Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Device Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $device?->name) }}" required placeholder="e.g. Lobby Fingerprint Scanner">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $device?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" id="device-type" required>
                            <option value="">Select Type</option>
                            <option value="biometric" {{ old('type', $device?->type) == 'biometric' ? 'selected' : '' }}>Biometric</option>
                            <option value="printer" {{ old('type', $device?->type) == 'printer' ? 'selected' : '' }}>Printer</option>
                            <option value="smart_lock" {{ old('type', $device?->type) == 'smart_lock' ? 'selected' : '' }}>Smart Lock</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Device Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand', $device?->brand) }}" placeholder="e.g. ZKTeco, Epson, Onity">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model', $device?->model) }}" placeholder="e.g. uFace 800">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                        <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $device?->serial_number) }}" required placeholder="e.g. ZKT-2024-001">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location', $device?->location) }}" placeholder="e.g. Main Lobby, Floor 1">
                    </div>
                    <div class="col-md-4 mb-3 device-room" style="{{ $device?->type !== 'smart_lock' ? 'display:none' : '' }}">
                        <label class="form-label">Linked Room</label>
                        <select name="room_id" class="form-select">
                            <option value="">Select Room (for smart locks)</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $device?->room_id) == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $device?->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $device?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status', $device?->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Network Configuration</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">IP Address</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address', $device?->ip_address) }}" placeholder="e.g. 192.168.1.100">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Port</label>
                        <input type="number" name="port" class="form-control" value="{{ old('port', $device?->port) }}" placeholder="e.g. 8080">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" name="api_key" class="form-control" value="{{ old('api_key', $device?->api_key) }}" placeholder="Device API key or token">
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ $device ? 'Update' : 'Add' }} Device</button>
                    <a href="{{ route('admin.integrations.devices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('#device-type').on('change', function() {
    if ($(this).val() === 'smart_lock') {
        $('.device-room').show();
    } else {
        $('.device-room').hide();
        $('select[name="room_id"]').val('');
    }
});
</script>
@endsection
