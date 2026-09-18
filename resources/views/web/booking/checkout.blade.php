@extends('web.layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
<main class="booking-page">
    <section class="page-hero" style="min-height: 260px;">
        <div class="page-hero__bg" style="background-image: url('{{ asset('assets/admin/img/backgrounds/14.jpg') }}');"></div>
        <div class="page-hero__overlay"></div>
        <div class="page-hero__content" style="padding: 32px 0;">
            <p class="kicker">Step 2 of 3</p>
            <h1>Complete Your Booking</h1>
        </div>
    </section>

    <section class="section-shell" style="padding: 40px 0 60px;">
        <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 40px; align-items: start;">
            {{-- GUEST FORM --}}
            <div>
                <h2 style="margin: 0 0 24px; font-size: 26px;">Guest Details</h2>
                <form id="checkout-form" class="contact-form" action="{{ route('web.booking.confirm') }}" method="POST">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="check_in" value="{{ $checkIn }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut }}">
                    <input type="hidden" name="adults" value="{{ $adults }}">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <label>
                            <span>First Name *</span>
                            <input type="text" name="first_name" placeholder="Enter your first name" required>
                        </label>
                        <label>
                            <span>Email Address *</span>
                            <input type="email" name="email" placeholder="you@example.com" required>
                        </label>
                    </div>
                    <label>
                        <span>Phone Number</span>
                        <input type="tel" name="phone" placeholder="+91 98765 43210">
                    </label>

                    <div style="padding: 18px; border: 1px solid #dce4df; background: #fff; margin-top: 8px;">
                        <h4 style="margin: 0 0 10px; font-size: 15px;">Booking Summary</h4>
                        <p style="margin: 0 0 6px; font-size: 14px; color: #66716b;">{{ $hotel->name }} &middot; Room {{ $room->room_number }} ({{ $room->roomType->name ?? 'Standard' }})</p>
                        <p style="margin: 0 0 6px; font-size: 14px; color: #66716b;">Check-in: <strong>{{ $checkIn }}</strong> &middot; Check-out: <strong>{{ $checkOut }}</strong></p>
                        <p style="margin: 0; font-size: 14px; color: #66716b;">{{ $nights }} night(s) &middot; {{ $adults }} guest(s)</p>
                    </div>

                    <button type="submit" class="btn-primary-site" style="justify-content: center; margin-top: 8px;">
                        <i class="fas fa-check-circle"></i> Confirm Reservation
                    </button>
                </form>
            </div>

            {{-- PRICE SUMMARY --}}
            <div style="position: sticky; top: 90px;">
                <div style="padding: 28px; border: 1px solid #dce4df; background: #fff; box-shadow: 0 14px 36px rgba(23,33,29,0.08);">
                    <h3 style="margin: 0 0 18px; font-size: 20px; font-weight: 800;">Price Details</h3>

                    <dl class="booking-summary__lines">
                        <div>
                            <dt>Room Type</dt>
                            <dd>{{ $room->roomType->name ?? 'Standard' }}</dd>
                        </div>
                        <div>
                            <dt>Rate / Night</dt>
                            <dd>{{ $currencySymbol }}{{ number_format($ratePerNight, 0) }}</dd>
                        </div>
                        <div>
                            <dt>Nights</dt>
                            <dd>{{ $nights }}</dd>
                        </div>
                        <div>
                            <dt>Subtotal</dt>
                            <dd>{{ $currencySymbol }}{{ number_format($subtotal, 0) }}</dd>
                        </div>
                        <div>
                            <dt>Taxes ({{ $taxRate }}%)</dt>
                            <dd>{{ $currencySymbol }}{{ number_format($tax, 0) }}</dd>
                        </div>
                    </dl>

                    <div class="booking-summary__total">
                        <span>Total Amount</span>
                        <strong style="font-size: 28px;">{{ $currencySymbol }}{{ number_format($total, 0) }}</strong>
                    </div>

                    <div style="margin-top: 18px; padding: 14px; background: rgba(20,98,79,0.06); border: 1px solid rgba(20,98,79,0.15); font-size: 13px; color: #14624f; text-align: center;">
                        <i class="fas fa-shield-alt"></i> Secure booking. Your information is safe with us.
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
