@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-test-tube"></i></div>
            <div>
                <h4 class="m-page-title">API Test Dashboard</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Channel Manager</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Test API</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.channel-manager.channels.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="row mb-4">
        @foreach($channels as $channel)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100" style="border-top: 3px solid {{ match($channel->provider) {
                'booking_com' => '#003580',
                'expedia' => '#FBCE04',
                'agoda' => '#5C2D91',
                'airbnb' => '#FF5A5F',
                'makemytrip' => '#E23738',
                'goibibo' => '#F05A28',
                'trip_com' => '#287DFA',
                'hostelworld' => '#2B9EB3',
                default => '#6c757d',
            } }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $channel->name }}</h6>
                    <span class="badge bg-success">Active</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        <i class="bx bx-building-house"></i> {{ $channel->property_id_on_ota }}<br>
                        <i class="bx bx-link"></i> {{ $channel->endpoint_url }}<br>
                        <i class="bx bx-time"></i> Last sync: {{ $channel->last_synced_at?->diffForHumans() ?? 'Never' }}
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-sm btn-outline-info btn-test-pull" data-url="{{ route('admin.channel-manager.test-api.pull', $channel) }}" data-name="{{ $channel->name }}">
                            <i class="bx bx-download"></i> Pull
                        </button>
                        <button class="btn btn-sm btn-outline-warning btn-test-rates" data-url="{{ route('admin.channel-manager.test-api.push-rates', $channel) }}" data-name="{{ $channel->name }}">
                            <i class="bx bx-dollar"></i> Push Rates
                        </button>
                        <button class="btn btn-sm btn-outline-success btn-test-avail" data-url="{{ route('admin.channel-manager.test-api.push-availability', $channel) }}" data-name="{{ $channel->name }}">
                            <i class="bx bx-calendar"></i> Push Avail
                        </button>
                        <button class="btn btn-sm btn-primary btn-test-full" data-url="{{ route('admin.channel-manager.test-api.full-sync', $channel) }}" data-name="{{ $channel->name }}">
                            <i class="bx bx-sync"></i> Full Sync
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Test Results</h5>
        </div>
        <div class="card-body">
            <div id="test-results" class="alert alert-info">Click a test button above to simulate API flow</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Sync Logs</h5>
            <a href="{{ route('admin.channel-manager.sync-logs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Channel</th>
                        <th>Direction</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($syncLogs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->otaChannel?->name ?? '-' }}</td>
                        <td><span class="badge bg-{{ $log->direction === 'inbound' ? 'info' : 'warning' }}">{{ $log->direction }}</span></td>
                        <td>{{ $log->action }}</td>
                        <td><span class="badge bg-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'secondary') }}">{{ $log->status }}</span></td>
                        <td>{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No sync logs yet. Run a test above!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function testApi(url, button, name) {
    var resultsDiv = document.getElementById('test-results');
    resultsDiv.className = 'alert alert-info';
    resultsDiv.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Testing ' + name + '...';

    $.ajax({
        url: url,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                resultsDiv.className = 'alert alert-success';
                resultsDiv.innerHTML = '<strong><i class="bx bx-check-circle"></i> ' + response.message + '</strong><pre class="mt-2 mb-0" style="max-height:300px;overflow:auto;font-size:12px;">' + JSON.stringify(response.data, null, 2) + '</pre>';
            } else {
                resultsDiv.className = 'alert alert-danger';
                resultsDiv.innerHTML = '<strong><i class="bx bx-error"></i> ' + response.message + '</strong>';
            }
            setTimeout(function() { location.reload(); }, 2000);
        },
        error: function(xhr) {
            resultsDiv.className = 'alert alert-danger';
            resultsDiv.innerHTML = '<strong><i class="bx bx-error"></i> Request failed: ' + (xhr.responseJSON?.message || xhr.statusText) + '</strong>';
        }
    });
}

$('.btn-test-pull').click(function() { testApi($(this).data('url'), $(this), $(this).data('name') + ' - Pull Reservations'); });
$('.btn-test-rates').click(function() { testApi($(this).data('url'), $(this), $(this).data('name') + ' - Push Rates'); });
$('.btn-test-avail').click(function() { testApi($(this).data('url'), $(this), $(this).data('name') + ' - Push Availability'); });
$('.btn-test-full').click(function() { testApi($(this).data('url'), $(this), $(this).data('name') + ' - Full Sync'); });
</script>
@endsection
