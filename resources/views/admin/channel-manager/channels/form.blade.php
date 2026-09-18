@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-link"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($channel) ? 'Edit OTA Channel' : 'Create OTA Channel' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Channel Manager</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.channel-manager.channels.index') }}">OTA Channels</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($channel) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.channel-manager.channels.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($channel) ? route('admin.channel-manager.channels.update', $channel) : route('admin.channel-manager.channels.store') }}" method="POST">
                @csrf
                @if(isset($channel)) @method('PUT') @endif

                <div class="m-section-divider">Channel Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $channel?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Channel Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $channel?->name) }}" required placeholder="e.g. Booking.com - Main">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Provider <span class="text-danger">*</span></label>
                        <select name="provider" class="form-select" required>
                            <option value="">Select Provider</option>
                            <option value="booking_com" {{ old('provider', $channel?->provider) == 'booking_com' ? 'selected' : '' }}>Booking.com</option>
                            <option value="expedia" {{ old('provider', $channel?->provider) == 'expedia' ? 'selected' : '' }}>Expedia</option>
                            <option value="agoda" {{ old('provider', $channel?->provider) == 'agoda' ? 'selected' : '' }}>Agoda</option>
                            <option value="airbnb" {{ old('provider', $channel?->provider) == 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                            <option value="makemytrip" {{ old('provider', $channel?->provider) == 'makemytrip' ? 'selected' : '' }}>MakeMyTrip</option>
                            <option value="goibibo" {{ old('provider', $channel?->provider) == 'goibibo' ? 'selected' : '' }}>Goibibo</option>
                            <option value="trip_com" {{ old('provider', $channel?->provider) == 'trip_com' ? 'selected' : '' }}>Trip.com</option>
                            <option value="hostelworld" {{ old('provider', $channel?->provider) == 'hostelworld' ? 'selected' : '' }}>Hostelworld</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Property ID on OTA</label>
                        <input type="text" name="property_id_on_ota" class="form-control" value="{{ old('property_id_on_ota', $channel?->property_id_on_ota) }}" placeholder="OTA property identifier">
                    </div>
                </div>

                <div class="m-section-divider">API Configuration</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" name="api_key" class="form-control" value="{{ old('api_key', $channel?->api_key) }}" placeholder="API Key">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">API Secret</label>
                        <input type="password" name="api_secret" class="form-control" value="{{ old('api_secret', $channel?->api_secret) }}" placeholder="API Secret">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Endpoint URL</label>
                        <input type="url" name="endpoint_url" class="form-control" value="{{ old('endpoint_url', $channel?->endpoint_url) }}" placeholder="https://api.ota-provider.com/v1">
                    </div>
                </div>

                <div class="m-section-divider">Sync Settings</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sync Rates</label>
                        <select name="sync_rates" class="form-select">
                            <option value="1" {{ old('sync_rates', $channel?->sync_rates ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('sync_rates', $channel?->sync_rates) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sync Availability</label>
                        <select name="sync_availability" class="form-select">
                            <option value="1" {{ old('sync_availability', $channel?->sync_availability ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('sync_availability', $channel?->sync_availability) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sync Reservations</label>
                        <select name="sync_reservations" class="form-select">
                            <option value="1" {{ old('sync_reservations', $channel?->sync_reservations ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('sync_reservations', $channel?->sync_reservations) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Auto Sync</label>
                        <select name="auto_sync" class="form-select">
                            <option value="1" {{ old('auto_sync', $channel?->auto_sync) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('auto_sync', $channel?->auto_sync ?? 0) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>

                @if(isset($channel))
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $channel->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $channel->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                @endif

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($channel) ? 'Update' : 'Create' }} Channel</button>
                    <a href="{{ route('admin.channel-manager.channels.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
