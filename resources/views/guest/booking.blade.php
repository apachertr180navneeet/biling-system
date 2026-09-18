@extends('web.layouts.app')

@section('title', 'My Booking - ' . ($company->name ?? config('app.name')))

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
    .badge--confirmed { background: #e8f5e9; color: #14624f; }
    .badge--checked-in { background: #e3f2fd; color: #1a73e8; }
    .badge--checked-out { background: #f5f5f5; color: #666; }

    .guest-room-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 14px 16px; background: #fbfaf7; border: 1px solid #f0f0f0;
        margin-bottom: 10px;
    }
    .guest-room-item strong { font-size: 15px; }
    .guest-room-item small { color: #66716b; display: block; margin-top: 2px; }

    .guest-summary {
        padding: 22px;
    }
    .guest-summary__row {
        display: flex; justify-content: space-between; padding: 8px 0;
        font-size: 14px;
    }
    .guest-summary__row span { color: #66716b; }
    .guest-summary__row strong { color: #17211d; }
    .guest-summary__row--discount span { color: #14624f; }
    .guest-summary__total {
        display: flex; justify-content: space-between; padding: 14px 0 0;
        border-top: 2px solid #14624f; margin-top: 10px;
    }
    .guest-summary__total strong { font-size: 20px; color: #14624f; }
    .guest-summary__balance {
        display: flex; justify-content: space-between; padding: 8px 0 0;
        font-size: 14px;
    }

    .guest-payment {
        padding: 14px 16px; display: flex; justify-content: space-between;
        border-bottom: 1px solid #f0f0f0; font-size: 14px;
    }
    .guest-payment:last-child { border: none; }
    .guest-payment small { color: #66716b; display: block; }
</style>
@endsection

@section('content')
<section class="guest-page">
    <div class="section-shell">

        <div class="guest-topbar">
            <h1><i class="fas fa-calendar-check" style="color:#bd8c3a;"></i> My Booking</h1>
            <a href="{{ route('guest.dashboard') }}" class="btn-outline-site" style="padding:10px 22px; font-size:14px; color:#66716b; border-color:#dce4df;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="guest-grid">
            <div>
                <div class="guest-card" style="margin-bottom:24px;">
                    <div class="guest-card__header">
                        <h3>Reservation Details</h3>
                    </div>
                    <div class="guest-card__body">
                        <div class="guest-detail-row">
                            <strong>Reservation #</strong>
                            <span>{{ $reservation->reservation_number }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Hotel</strong>
                            <span>{{ $reservation->hotel->name }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Source</strong>
                            <span>{{ ucfirst(str_replace('-', ' ', $reservation->booking_source)) }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Status</strong>
                            <span class="badge badge--{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Check-in</strong>
                            <span>{{ $reservation->check_in_date->format('l, M d, Y') }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Check-out</strong>
                            <span>{{ $reservation->check_out_date->format('l, M d, Y') }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Nights</strong>
                            <span>{{ $reservation->check_in_date->diffInDays($reservation->check_out_date) }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Guests</strong>
                            <span>{{ $reservation->adults }} Adult(s){{ $reservation->children ? ', ' . $reservation->children . ' Child(ren)' : '' }}</span>
                        </div>
                    </div>
                </div>

                <div class="guest-card" style="margin-bottom:24px;">
                    <div class="guest-card__header">
                        <h3>Room Details</h3>
                    </div>
                    <div class="guest-card__body">
                        @foreach($reservation->rooms as $rr)
                        <div class="guest-room-item">
                            <div>
                                <strong>{{ $rr->roomType->name ?? 'Room' }}</strong>
                                <small>Room {{ $rr->room?->room_number ?? '-' }} &middot; {{ $rr->room?->bedType->name ?? '' }}</small>
                            </div>
                            <div style="text-align:right;">
                                <strong style="font-size:16px; color:#14624f;">${{ number_format($rr->rate_per_night, 2) }}</strong>
                                <small>/night</small>
                                <br>
                                <small>{{ $rr->check_in_date->format('M d') }} - {{ $rr->check_out_date->format('M d') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($reservation->checkIn)
                <div class="guest-card">
                    <div class="guest-card__header">
                        <h3>Check-in Information</h3>
                    </div>
                    <div class="guest-card__body">
                        <div class="guest-detail-row">
                            <strong>Actual Check-in</strong>
                            <span>{{ $reservation->checkIn->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="guest-detail-row">
                            <strong>Key Issued</strong>
                            <span>{{ $reservation->checkIn->key_issued ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div>
                <div class="guest-card" style="margin-bottom:24px;">
                    <div class="guest-card__header">
                        <h3>Payment Summary</h3>
                    </div>
                    <div class="guest-card__body guest-summary">
                        <div class="guest-summary__row">
                            <span>Room Charges</span>
                            <strong>${{ number_format($reservation->total_amount - $reservation->tax_amount + $reservation->discount_amount, 2) }}</strong>
                        </div>
                        @if($reservation->discount_amount > 0)
                        <div class="guest-summary__row guest-summary__row--discount">
                            <span>Discount</span>
                            <strong>-${{ number_format($reservation->discount_amount, 2) }}</strong>
                        </div>
                        @endif
                        <div class="guest-summary__row">
                            <span>Taxes & Fees</span>
                            <strong>${{ number_format($reservation->tax_amount, 2) }}</strong>
                        </div>
                        <div class="guest-summary__total">
                            <strong>Total</strong>
                            <strong>${{ number_format($reservation->total_amount, 2) }}</strong>
                        </div>
                        <div class="guest-summary__row" style="border:none;">
                            <span>Paid</span>
                            <strong style="color:#14624f;">${{ number_format($reservation->paid_amount, 2) }}</strong>
                        </div>
                        <div class="guest-summary__balance">
                            <span style="font-weight:700; color:#17211d;">Balance Due</span>
                            <strong style="color:{{ $reservation->total_amount - $reservation->paid_amount > 0 ? '#c62828' : '#14624f' }};">
                                ${{ number_format($reservation->total_amount - $reservation->paid_amount, 2) }}
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="guest-card">
                    <div class="guest-card__header">
                        <h3>Payment History</h3>
                    </div>
                    <div class="guest-card__body" style="padding:0;">
                        @forelse($reservation->payments as $payment)
                        <div class="guest-payment">
                            <div>
                                <strong>{{ ucfirst(str_replace('-', ' ', $payment->payment_method)) }}</strong>
                                <small>{{ $payment->payment_date->format('M d, Y') }}</small>
                            </div>
                            <strong style="color:#14624f;">${{ number_format($payment->amount, 2) }}</strong>
                        </div>
                        @empty
                        <div style="text-align:center; padding:32px; color:#66716b; font-size:14px;">
                            No payments recorded
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
