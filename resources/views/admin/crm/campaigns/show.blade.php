@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-paper-plane"></i></div>
            <div>
                <h4 class="m-page-title">Campaign: {{ $campaign->name }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.crm.campaigns.index') }}">Campaigns</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $campaign->name }}</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.crm.campaigns.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
            @if($campaign->status !== 'sent')
            <a href="{{ route('admin.crm.campaigns.send', $campaign) }}" class="btn btn-success"><i class="bx bx-send me-1"></i> Send Now</a>
            @endif
        </div>
    </div>

    <!-- Campaign Metrics cards if campaign is sent -->
    @if($campaign->status === 'sent')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2 bg-label-info rounded">
                        <i class="bx bx-group fs-3"></i>
                    </div>
                    <span class="d-block text-muted">Total Recipients</span>
                    <h3 class="card-title mb-0 fw-semibold">{{ $campaign->total_recipients }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2 bg-label-success rounded">
                        <i class="bx bx-check-circle fs-3"></i>
                    </div>
                    <span class="d-block text-muted">Successful Deliveries</span>
                    <h3 class="card-title mb-0 fw-semibold">{{ $campaign->successful_deliveries }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2 bg-label-danger rounded">
                        <i class="bx bx-x-circle fs-3"></i>
                    </div>
                    <span class="d-block text-muted">Failed Deliveries</span>
                    <h3 class="card-title mb-0 fw-semibold">{{ $campaign->total_recipients - $campaign->successful_deliveries }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2 bg-label-primary rounded">
                        <i class="bx bx-bar-chart-alt-2 fs-3"></i>
                    </div>
                    <span class="d-block text-muted">Delivery Rate</span>
                    @php
                        $rate = $campaign->total_recipients > 0 ? round(($campaign->successful_deliveries / $campaign->total_recipients) * 100, 1) : 0;
                    @endphp
                    <h3 class="card-title mb-0 fw-semibold">{{ $rate }}%</h3>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Campaign Details column -->
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Campaign Info</h5>
                    @php
                        $statusColors = ['draft' => 'secondary', 'scheduled' => 'warning', 'sending' => 'info', 'sent' => 'success', 'failed' => 'danger'];
                        $channelBadges = [
                            'email' => '<span class="badge bg-label-info"><i class="bx bx-envelope me-1"></i> Email</span>',
                            'sms' => '<span class="badge bg-label-primary"><i class="bx bx-message-detail me-1"></i> SMS</span>',
                            'whatsapp' => '<span class="badge bg-label-success"><i class="bx bxl-whatsapp me-1"></i> WhatsApp</span>'
                        ];
                        $audienceLabels = ['all_guests' => 'All Guests', 'loyalty_members' => 'Loyalty Members', 'recent_guests' => 'Recent Guests'];
                    @endphp
                    <span class="badge bg-label-{{ $statusColors[$campaign->status] ?? 'secondary' }}">{{ ucfirst($campaign->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Campaign Channel</label>
                            <p class="mb-0">{!! $channelBadges[$campaign->channel] ?? $campaign->channel !!}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Target Audience</label>
                            <p class="mb-0 fw-semibold">{{ $audienceLabels[$campaign->target_audience] ?? $campaign->target_audience }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Scheduled At</label>
                            <p class="mb-0">{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('d-m-Y H:i') : 'Manual Dispatch' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Sent At</label>
                            <p class="mb-0">{{ $campaign->sent_at ? $campaign->sent_at->format('d-m-Y H:i') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message preview card -->
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Message Body</h5></div>
                <div class="card-body">
                    @if($campaign->channel === 'email' && $campaign->subject)
                    <div class="mb-3">
                        <label class="form-label text-muted mb-0">Subject Line</label>
                        <p class="mb-0 fw-semibold text-primary">{{ $campaign->subject }}</p>
                    </div>
                    <hr class="mt-0">
                    @endif
                    <label class="form-label text-muted mb-1">Content Preview</label>
                    <div class="p-3 border rounded bg-light" style="white-space: pre-wrap; font-family: inherit;">{{ $campaign->content }}</div>
                </div>
            </div>
        </div>

        <!-- Target details or status explanation -->
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Campaign Manager Notes</h5></div>
                <div class="card-body">
                    @if($campaign->status === 'draft')
                    <div class="alert alert-secondary mb-0">
                        <h6 class="alert-heading fw-bold mb-1">Campaign is in Draft Mode</h6>
                        <p class="mb-0 small">You can edit the settings or compose message contents. Click <strong>Send Now</strong> at the top to simulate immediate dispatch to the target list.</p>
                    </div>
                    @elseif($campaign->status === 'scheduled')
                    <div class="alert alert-warning mb-0">
                        <h6 class="alert-heading fw-bold mb-1">Campaign is Scheduled</h6>
                        <p class="mb-0 small">This campaign will be automatically dispatched at the scheduled time, or click <strong>Send Now</strong> to trigger execution immediately.</p>
                    </div>
                    @elseif($campaign->status === 'sent')
                    <div class="alert alert-success mb-0">
                        <h6 class="alert-heading fw-bold mb-1">Campaign Completed Successfully</h6>
                        <p class="mb-0 small">Simulated dispatch finished. Delivery logs, metrics, and individual recipient tracking are displayed below.</p>
                    </div>
                    @else
                    <div class="alert alert-danger mb-0">
                        <h6 class="alert-heading fw-bold mb-1">Dispatch Failed</h6>
                        <p class="mb-0 small">An error occurred during sending simulation. Check logs below for details.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recipients log -->
    @if($campaign->logs->count() > 0)
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Delivery Logs</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Contact Detail</th>
                            <th>Sent At</th>
                            <th>Status</th>
                            <th>Error Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaign->logs as $log)
                        <tr>
                            <td>
                                @if($log->guest)
                                    {{ $log->guest->first_name }} {{ $log->guest->last_name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td><code>{{ $log->recipient_contact }}</code></td>
                            <td>{{ $log->sent_at ? $log->sent_at->format('d-m-Y H:i') : '-' }}</td>
                            <td>
                                @php $ls = ['pending' => 'warning', 'sent' => 'success', 'failed' => 'danger']; @endphp
                                <span class="badge bg-label-{{ $ls[$log->status] ?? 'secondary' }}">{{ ucfirst($log->status) }}</span>
                            </td>
                            <td>
                                @if($log->error_message)
                                    <span class="text-danger small"><i class="bx bx-error-circle me-1"></i> {{ $log->error_message }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
