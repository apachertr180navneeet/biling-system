@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-network-chart"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($mapping) ? 'Edit Room Mapping' : 'Create Room Mapping' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Channel Manager</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.channel-manager.mappings.index') }}">Room Mappings</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($mapping) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.channel-manager.mappings.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($mapping) ? route('admin.channel-manager.mappings.update', $mapping) : route('admin.channel-manager.mappings.store') }}" method="POST">
                @csrf
                @if(isset($mapping)) @method('PUT') @endif

                <div class="m-section-divider">Mapping Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">OTA Channel <span class="text-danger">*</span></label>
                        <select name="ota_channel_id" class="form-select" required>
                            <option value="">Select Channel</option>
                            @foreach($channels as $channel)
                            <option value="{{ $channel->id }}" {{ old('ota_channel_id', $mapping?->ota_channel_id) == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Room Type <span class="text-danger">*</span></label>
                        <select name="room_type_id" class="form-select" required>
                            <option value="">Select Room Type</option>
                            @foreach($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" {{ old('room_type_id', $mapping?->room_type_id) == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">OTA Room Type ID</label>
                        <input type="text" name="ota_room_type_id" class="form-control" value="{{ old('ota_room_type_id', $mapping?->ota_room_type_id) }}" placeholder="Room type ID on OTA">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">OTA Room Name</label>
                        <input type="text" name="ota_room_name" class="form-control" value="{{ old('ota_room_name', $mapping?->ota_room_name) }}" placeholder="Room name displayed on OTA">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rate Multiplier <span class="text-danger">*</span></label>
                        <input type="number" name="rate_multiplier" class="form-control" value="{{ old('rate_multiplier', $mapping?->rate_multiplier ?? '1.00') }}" min="0.01" max="9.99" step="0.01" required>
                        <small class="text-muted">1.00 = same rate, 1.10 = 10% markup</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sync Rates</label>
                        <select name="sync_rates" class="form-select">
                            <option value="1" {{ old('sync_rates', $mapping?->sync_rates ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('sync_rates', $mapping?->sync_rates) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sync Availability</label>
                        <select name="sync_availability" class="form-select">
                            <option value="1" {{ old('sync_availability', $mapping?->sync_availability ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('sync_availability', $mapping?->sync_availability) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                @if(isset($mapping))
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $mapping->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $mapping->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                @endif

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($mapping) ? 'Update' : 'Create' }} Mapping</button>
                    <a href="{{ route('admin.channel-manager.mappings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
