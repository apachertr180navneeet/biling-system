@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-cog"></i></div>
            <div>
                <h4 class="m-page-title">Channel Settings</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Channel Manager</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Settings</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.channel-manager.channels.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back to Channels</a>
    </div>

    <form action="{{ route('admin.channel-manager.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Select Hotel</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" id="hotel-select" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @php
                            $hotels = \App\Models\Hotel::where('status', 'active')->get();
                            @endphp
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach($allProviders as $provider => $info)
            @php
            $existing = $channels->firstWhere('provider', $provider);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100" style="border-top: 3px solid {{ $info['color'] }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx {{ $info['icon'] }} fs-3" style="color: {{ $info['color'] }}"></i>
                                <h5 class="card-title mb-0">{{ $info['name'] }}</h5>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="providers[{{ $provider }}][enabled]" value="1" id="toggle_{{ $provider }}" {{ $existing && $existing->status === 'active' ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="provider-settings" id="settings_{{ $provider }}" style="{{ !$existing || $existing->status !== 'active' ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                            <input type="hidden" name="providers[{{ $provider }}][existing_id]" value="{{ $existing?->id }}">

                            <div class="mb-3">
                                <label class="form-label">API Key</label>
                                <input type="text" name="providers[{{ $provider }}][api_key]" class="form-control form-control-sm" value="{{ old('providers.'.$provider.'.api_key', $existing?->api_key) }}" placeholder="Enter API Key">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">API Secret</label>
                                <input type="password" name="providers[{{ $provider }}][api_secret]" class="form-control form-control-sm" value="{{ old('providers.'.$provider.'.api_secret', $existing?->api_secret) }}" placeholder="Enter API Secret">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Property ID on {{ $info['name'] }}</label>
                                <input type="text" name="providers[{{ $provider }}][property_id]" class="form-control form-control-sm" value="{{ old('providers.'.$provider.'.property_id', $existing?->property_id_on_ota) }}" placeholder="Property/List ID">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Endpoint URL</label>
                                <input type="url" name="providers[{{ $provider }}][endpoint_url]" class="form-control form-control-sm" value="{{ old('providers.'.$provider.'.endpoint_url', $existing?->endpoint_url) }}" placeholder="https://api.{{ str_replace('_', '', $provider) }}.com">
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                @if($existing)
                                <span class="badge bg-success">Connected</span>
                                <small class="text-muted">Last sync: {{ $existing->last_synced_at?->diffForHumans() ?? 'Never' }}</small>
                                @else
                                <span class="badge bg-secondary">Not Configured</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> Save Settings</button>
                    <a href="{{ route('admin.channel-manager.channels.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
document.querySelectorAll('.form-check-input').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        var provider = this.id.replace('toggle_', '');
        var settings = document.getElementById('settings_' + provider);
        if (this.checked) {
            settings.style.opacity = '1';
            settings.style.pointerEvents = 'auto';
        } else {
            settings.style.opacity = '0.5';
            settings.style.pointerEvents = 'none';
        }
    });
});
</script>
@endsection
