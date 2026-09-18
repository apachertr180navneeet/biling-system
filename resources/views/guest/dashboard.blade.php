@extends('web.layouts.app')

@section('title', 'Guest Portal - ' . ($company->name ?? config('app.name')))

@section('style')
<style>
    .guest-page { padding: 48px 0; min-height: 70vh; }

    .guest-topbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 36px; flex-wrap: wrap; gap: 14px;
    }
    .guest-topbar h1 { margin: 0; font-size: 28px; font-weight: 800; }
    .guest-topbar p { margin: 2px 0 0; color: #66716b; font-size: 14px; }
    .guest-topbar__actions { display: flex; gap: 10px; }

    .guest-stats {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;
        margin-bottom: 36px;
    }
    .guest-stat {
        background: #fff; border: 1px solid #dce4df; padding: 24px;
        text-align: center;
    }
    .guest-stat__icon {
        width: 48px; height: 48px; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; margin: 0 auto 12px;
        font-size: 20px; color: #fff;
    }
    .guest-stat__icon--pending { background: #bd8c3a; }
    .guest-stat__icon--progress { background: #1a73e8; }
    .guest-stat__icon--done { background: #14624f; }
    .guest-stat__icon--nights { background: #17211d; }
    .guest-stat h3 { margin: 0; font-size: 32px; font-weight: 800; color: #17211d; }
    .guest-stat span { font-size: 13px; color: #66716b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

    .guest-grid {
        display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px; align-items: start;
    }

    .guest-card {
        background: #fff; border: 1px solid #dce4df; overflow: hidden;
    }
    .guest-card__header {
        display: flex; justify-content: space-between; align-items: center;
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
    .badge--confirmed { background: #e8f5e9; color: #14624f; }
    .badge--checked-in { background: #e3f2fd; color: #1a73e8; }
    .badge--checked-out { background: #f5f5f5; color: #666; }
    .badge--pending { background: #fff3e0; color: #bd8c3a; }
    .badge--in-progress { background: #e3f2fd; color: #1a73e8; }
    .badge--completed { background: #e8f5e9; color: #14624f; }
    .badge--cancelled { background: #fce4ec; color: #c62828; }
    .badge--low { background: #f5f5f5; color: #666; }
    .badge--medium { background: #e3f2fd; color: #1a73e8; }
    .badge--high { background: #fff3e0; color: #bd8c3a; }
    .badge--urgent { background: #fce4ec; color: #c62828; }

    .guest-table { width: 100%; border-collapse: collapse; }
    .guest-table th {
        text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #66716b; padding: 10px 12px;
        border-bottom: 2px solid #dce4df;
    }
    .guest-table td {
        padding: 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px;
        color: #17211d;
    }
    .guest-table tr:hover td { background: #fbfaf7; }

    .guest-empty {
        text-align: center; padding: 48px 24px; color: #66716b;
    }
    .guest-empty i { font-size: 48px; color: #dce4df; display: block; margin-bottom: 14px; }

    .guest-room-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 14px 16px; background: #fbfaf7; border: 1px solid #f0f0f0;
        margin-bottom: 10px;
    }
    .guest-room-item strong { font-size: 15px; }
    .guest-room-item small { color: #66716b; display: block; margin-top: 2px; }
    .guest-room-item .text-right { text-align: right; }
    .guest-room-item .text-right strong { font-size: 16px; color: #14624f; }
    .guest-room-item .text-right small { color: #66716b; }

    @media (max-width: 900px) {
        .guest-stats { grid-template-columns: repeat(2, 1fr); }
        .guest-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .guest-stats { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<section class="guest-page">
    <div class="section-shell">

        <div class="guest-topbar">
            <div>
                <h1>Welcome, {{ $guest->first_name }}!</h1>
                <p>Reservation <strong>{{ $reservation->reservation_number }}</strong> &middot; {{ $reservation->hotel->name }}</p>
            </div>
            <div class="guest-topbar__actions">
                <a href="{{ route('guest.service-request.create') }}" class="btn-primary-site" style="padding:10px 22px; font-size:14px;">
                    <i class="fas fa-plus"></i> New Request
                </a>
                <a href="{{ route('guest.logout') }}" class="btn-outline-site" style="padding:10px 22px; font-size:14px; color:#66716b; border-color:#dce4df;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        @if(session('success'))
        <div style="background:#e8f5e9; border:1px solid rgba(20,98,79,0.2); padding:14px 18px; margin-bottom:24px; font-size:14px; color:#14624f;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="guest-stats">
            <div class="guest-stat">
                <div class="guest-stat__icon guest-stat__icon--pending"><i class="fas fa-clock"></i></div>
                <h3>{{ $stats['pending'] }}</h3>
                <span>Pending</span>
            </div>
            <div class="guest-stat">
                <div class="guest-stat__icon guest-stat__icon--progress"><i class="fas fa-spinner"></i></div>
                <h3>{{ $stats['in_progress'] }}</h3>
                <span>In Progress</span>
            </div>
            <div class="guest-stat">
                <div class="guest-stat__icon guest-stat__icon--done"><i class="fas fa-check-circle"></i></div>
                <h3>{{ $stats['completed'] }}</h3>
                <span>Completed</span>
            </div>
            <div class="guest-stat">
                <div class="guest-stat__icon guest-stat__icon--nights"><i class="fas fa-moon"></i></div>
                <h3>{{ $reservation->check_in_date->diffInDays($reservation->check_out_date) }}</h3>
                <span>Total Nights</span>
            </div>
        </div>

        <div class="guest-grid">
            <div class="guest-card">
                <div class="guest-card__header">
                    <h3><i class="fas fa-calendar-check" style="color:#bd8c3a;"></i> Booking Details</h3>
                    <a href="{{ route('guest.my-booking') }}" style="font-size:13px; font-weight:600; color:#14624f;">View Full <i class="fas fa-arrow-right" style="font-size:11px;"></i></a>
                </div>
                <div class="guest-card__body">
                    <div class="guest-detail-row">
                        <strong>Check-in</strong>
                        <span>{{ $reservation->check_in_date->format('M d, Y') }}</span>
                    </div>
                    <div class="guest-detail-row">
                        <strong>Check-out</strong>
                        <span>{{ $reservation->check_out_date->format('M d, Y') }}</span>
                    </div>
                    <div class="guest-detail-row">
                        <strong>Status</strong>
                        <span class="badge badge--{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span>
                    </div>
                    <div style="margin-top:14px;">
                        <strong style="font-size:13px; text-transform:uppercase; letter-spacing:0.5px; color:#66716b;">Rooms</strong>
                        @foreach($reservation->rooms as $rr)
                        <div class="guest-room-item" style="margin-top:10px;">
                            <div>
                                <strong>{{ $rr->roomType->name ?? 'Room' }}</strong>
                                <small>Room {{ $rr->room?->room_number ?? '-' }}</small>
                            </div>
                            <div class="text-right">
                                <strong>${{ number_format($rr->rate_per_night, 2) }}</strong>
                                <small>/night</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="guest-card">
                <div class="guest-card__header">
                    <h3><i class="fas fa-concierge-bell" style="color:#bd8c3a;"></i> Service Requests</h3>
                    <a href="{{ route('guest.service-request.create') }}" class="btn-primary-site" style="padding:6px 16px; font-size:13px;">
                        <i class="fas fa-plus"></i> New
                    </a>
                </div>
                <div class="guest-card__body" style="padding:0;">
                    @if($serviceRequests->isEmpty())
                    <div class="guest-empty">
                        <i class="fas fa-clipboard-list"></i>
                        <p>No service requests yet.<br>Need something? Create a request!</p>
                        <a href="{{ route('guest.service-request.create') }}" class="btn-primary-site" style="padding:10px 24px; font-size:14px;">
                            <i class="fas fa-plus"></i> Create Request
                        </a>
                    </div>
                    @else
                    <table class="guest-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceRequests as $req)
                            <tr>
                                <td style="font-size:12px; color:#66716b;">{{ $req->request_number }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $req->category)) }}</td>
                                <td>{{ Str::limit($req->subject, 28) }}</td>
                                <td><span class="badge badge--{{ $req->priority }}">{{ ucfirst($req->priority) }}</span></td>
                                <td><span class="badge badge--{{ $req->status }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span></td>
                                <td><a href="{{ route('guest.service-request.detail', $req) }}" style="color:#14624f; font-weight:600; font-size:13px;">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
