@extends('web.layouts.app')

@section('title', 'Request #' . $serviceRequest->request_number . ' - ' . ($company->name ?? config('app.name')))

@section('style')
<style>
    .guest-page { padding: 48px 0; min-height: 70vh; }

    .guest-topbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 36px; flex-wrap: wrap; gap: 14px;
    }
    .guest-topbar h1 { margin: 0; font-size: 28px; font-weight: 800; }

    .guest-grid {
        display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px; align-items: start;
    }

    .guest-card {
        background: #fff; border: 1px solid #dce4df; overflow: hidden;
    }
    .guest-card__header {
        padding: 18px 22px; border-bottom: 1px solid #dce4df;
    }
    .guest-card__header h3 { margin: 0; font-size: 16px; font-weight: 700; }
    .guest-card__body { padding: 22px; }

    .guest-detail-row {
        display: flex; justify-content: space-between; padding: 10px 0;
        border-bottom: 1px solid #f0f0f0; font-size: 14px;
    }
    .guest-detail-row:last-child { border: none; }
    .guest-detail-row strong { color: #17211d; }
    .guest-detail-row span { color: #66716b; }

    .badge {
        display: inline-block; padding: 4px 10px; font-size: 12px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.3px;
    }
    .badge--pending { background: #fff3e0; color: #bd8c3a; }
    .badge--in_progress { background: #e3f2fd; color: #1a73e8; }
    .badge--completed { background: #e8f5e9; color: #14624f; }
    .badge--cancelled { background: #fce4ec; color: #c62828; }
    .badge--low { background: #f5f5f5; color: #666; }
    .badge--medium { background: #e3f2fd; color: #1a73e8; }
    .badge--high { background: #fff3e0; color: #bd8c3a; }
    .badge--urgent { background: #fce4ec; color: #c62828; }

    .guest-description {
        background: #fbfaf7; border: 1px solid #f0f0f0; padding: 16px;
        font-size: 14px; line-height: 1.7; color: #17211d; margin-top: 14px;
    }

    .timeline { position: relative; padding-left: 28px; }
    .timeline::before {
        content: ''; position: absolute; left: 7px; top: 4px; bottom: 4px;
        width: 2px; background: #dce4df;
    }
    .timeline__item {
        position: relative; padding-bottom: 22px;
    }
    .timeline__item:last-child { padding-bottom: 0; }
    .timeline__item::before {
        content: ''; position: absolute; left: -24px; top: 5px;
        width: 10px; height: 10px; border-radius: 50%;
        background: #14624f; border: 2px solid #fbfaf7;
    }
    .timeline__item--pending::before { background: #bd8c3a; }
    .timeline__item--progress::before { background: #1a73e8; }
    .timeline__item--done::before { background: #14624f; }
    .timeline__item small { color: #66716b; font-size: 12px; display: block; margin-bottom: 2px; }
    .timeline__item p { margin: 0; font-size: 14px; font-weight: 600; color: #17211d; }
    .timeline__item p strong { color: #14624f; }

    @media (max-width: 900px) {
        .guest-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<section class="guest-page">
    <div class="section-shell">

        <div class="guest-topbar">
            <h1><i class="fas fa-clipboard-list" style="color:#bd8c3a;"></i> Request #{{ $serviceRequest->request_number }}</h1>
            <a href="{{ route('guest.dashboard') }}" class="btn-outline-site" style="padding:10px 22px; font-size:14px; color:#66716b; border-color:#dce4df;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="guest-grid">
            <div>
                <div class="guest-card" style="margin-bottom:24px;">
                    <div class="guest-card__header">
                        <h3>Request Details</h3>
                    </div>
                    <div class="guest-card__body">
                        <div class="guest-detail-row">
                            <strong>Request #</strong>
                            <span>{{ $serviceRequest->request_number }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Category</strong>
                            <span>{{ ucfirst(str_replace('_', ' ', $serviceRequest->category)) }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Subject</strong>
                            <span>{{ $serviceRequest->subject }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Priority</strong>
                            <span class="badge badge--{{ $serviceRequest->priority }}">{{ ucfirst($serviceRequest->priority) }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Status</strong>
                            <span class="badge badge--{{ $serviceRequest->status }}">{{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Room</strong>
                            <span>{{ $serviceRequest->room?->room_number ?? '-' }}</span>
                        </div>

                        @if($serviceRequest->description)
                        <div style="margin-top:18px;">
                            <strong style="font-size:13px; text-transform:uppercase; letter-spacing:0.5px; color:#66716b;">Description</strong>
                            <div class="guest-description">{{ $serviceRequest->description }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($serviceRequest->assignedTo)
                <div class="guest-card" style="margin-bottom:24px;">
                    <div class="guest-card__header">
                        <h3>Assigned Staff</h3>
                    </div>
                    <div class="guest-card__body">
                        <div class="guest-detail-row">
                            <strong>Name</strong>
                            <span>{{ $serviceRequest->assignedTo->name }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Assigned At</strong>
                            <span>{{ $serviceRequest->assigned_at?->format('M d, Y h:i A') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($serviceRequest->resolution_notes)
                <div class="guest-card">
                    <div class="guest-card__header">
                        <h3>Resolution Notes</h3>
                    </div>
                    <div class="guest-card__body">
                        <div class="guest-description" style="margin-top:0;">{{ $serviceRequest->resolution_notes }}</div>
                    </div>
                </div>
                @endif
            </div>

            <div>
                <div class="guest-card" style="margin-bottom:24px;">
                    <div class="guest-card__header">
                        <h3>Timeline</h3>
                    </div>
                    <div class="guest-card__body">
                        <div class="timeline">
                            <div class="timeline__item timeline__item--done">
                                <small>{{ $serviceRequest->created_at->format('M d, Y h:i A') }}</small>
                                <p>Request <strong>Created</strong></p>
                            </div>

                            @if($serviceRequest->assigned_at)
                            <div class="timeline__item timeline__item--progress">
                                <small>{{ $serviceRequest->assigned_at->format('M d, Y h:i A') }}</small>
                                <p>Assigned to <strong>{{ $serviceRequest->assignedTo?->name ?? 'Staff' }}</strong></p>
                            </div>
                            @endif

                            @if($serviceRequest->started_at)
                            <div class="timeline__item timeline__item--progress">
                                <small>{{ $serviceRequest->started_at->format('M d, Y h:i A') }}</small>
                                <p>Status changed to <strong>In Progress</strong></p>
                            </div>
                            @endif

                            @if($serviceRequest->completed_at)
                            <div class="timeline__item timeline__item--done">
                                <small>{{ $serviceRequest->completed_at->format('M d, Y h:i A') }}</small>
                                <p>Request <strong>Completed</strong></p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="guest-card">
                    <div class="guest-card__header">
                        <h3>Room Info</h3>
                    </div>
                    <div class="guest-card__body">
                        @if($serviceRequest->room)
                        <div class="guest-detail-row">
                            <strong>Room #</strong>
                            <span>{{ $serviceRequest->room->room_number }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Type</strong>
                            <span>{{ $serviceRequest->room->roomType?->name ?? '-' }}</span>
                        </div>
                        @else
                        <div style="text-align:center; padding:20px; color:#66716b; font-size:14px;">
                            No room assigned
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
