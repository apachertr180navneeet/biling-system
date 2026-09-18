@extends('web.layouts.app')

@section('title', 'Booking Confirmed - ' . config('app.name'))

@section('content')
<main class="booking-page">
    <section class="section-shell" style="padding: 60px 0;">
        <div style="max-width: 680px; margin: 0 auto; text-align: center;">
            <div style="width: 80px; height: 80px; background: rgba(20,98,79,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                <i class="fas fa-check-circle" style="font-size: 40px; color: #14624f;"></i>
            </div>

            <h1 style="margin: 0 0 12px; font-size: 36px;">Booking Confirmed!</h1>
            <p style="color: #66716b; font-size: 17px; margin: 0 0 36px;">Your reservation has been successfully created. We've sent a confirmation to your email.</p>

            <div style="padding: 28px; border: 1px solid #dce4df; background: #fff; text-align: left; box-shadow: 0 14px 36px rgba(23,33,29,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 18px; border-bottom: 1px solid #dce4df;">
                    <div>
                        <p style="margin: 0; font-size: 12px; text-transform: uppercase; font-weight: 800; color: #bd8c3a;">Reservation Number</p>
                        <h2 style="margin: 4px 0 0; font-size: 24px; color: #14624f;">{{ $reservation->reservation_number }}</h2>
                    </div>
                    <span style="padding: 6px 14px; background: rgba(20,98,79,0.1); color: #14624f; font-weight: 700; font-size: 13px; text-transform: uppercase;">{{ $reservation->status }}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                    <div>
                        <p style="margin: 0 0 4px; font-size: 12px; text-transform: uppercase; font-weight: 700; color: #66716b;">Hotel</p>
                        <p style="margin: 0; font-weight: 600;">{{ $hotel->name }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px; font-size: 12px; text-transform: uppercase; font-weight: 700; color: #66716b;">Room</p>
                        <p style="margin: 0; font-weight: 600;">Room {{ $room->room_number }} ({{ $room->roomType->name ?? 'Standard' }})</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px; font-size: 12px; text-transform: uppercase; font-weight: 700; color: #66716b;">Check-in</p>
                        <p style="margin: 0; font-weight: 600;">{{ $reservation->check_in_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px; font-size: 12px; text-transform: uppercase; font-weight: 700; color: #66716b;">Check-out</p>
                        <p style="margin: 0; font-weight: 600;">{{ $reservation->check_out_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px; font-size: 12px; text-transform: uppercase; font-weight: 700; color: #66716b;">Guest</p>
                        <p style="margin: 0; font-weight: 600;">{{ $guest->first_name }} {{ $guest->last_name ?? '' }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px; font-size: 12px; text-transform: uppercase; font-weight: 700; color: #66716b;">Nights</p>
                        <p style="margin: 0; font-weight: 600;">{{ $nights }}</p>
                    </div>
                </div>

                <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #dce4df;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #66716b; margin-bottom: 6px;">
                        <span>Room charges ({{ $nights }} nights)</span>
                        <span>{{ $currencySymbol }}{{ number_format($subtotal, 0) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #66716b; margin-bottom: 10px;">
                        <span>Taxes & fees ({{ $taxRate }}%)</span>
                        <span>{{ $currencySymbol }}{{ number_format($tax, 0) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 22px;">
                        <span>Total</span>
                        <span>{{ $currencySymbol }}{{ number_format($total, 0) }}</span>
                    </div>
                </div>
            </div>

            <div style="margin-top: 32px; display: flex; gap: 16px; justify-content: center;">
                <a href="{{ route('/') }}" class="btn-outline-site" style="color: #14624f; border-color: #dce4df;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="{{ route('web.booking') }}" class="btn-primary-site">
                    <i class="fas fa-plus"></i> New Booking
                </a>
            </div>
        </div>
    </section>
</main>
@endsection
