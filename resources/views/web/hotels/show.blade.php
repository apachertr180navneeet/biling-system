@extends('web.layouts.app')

@section('title', $hotel->name . ' - ' . ($company->name ?? config('app.name')))

@section('content')
<main>
    <section class="page-hero">
        <div class="page-hero__bg" style="background-image: url('{{ asset('assets/admin/img/backgrounds/' . (($hotel->id % 18) + 1) . '.jpg') }}');"></div>
        <div class="page-hero__overlay"></div>
        <div class="page-hero__content">
            <p class="kicker">Hotel</p>
            <h1>{{ $hotel->name }}</h1>
            @if($hotel->city || $hotel->country)
            <p><i class="fas fa-map-marker-alt"></i> {{ collect([$hotel->city, $hotel->country])->filter()->implode(', ') }}</p>
            @endif
        </div>
    </section>

    <section class="section-shell">
        <div class="hotel-detail__info">
            <div class="hotel-detail__desc">
                <h2>About This Hotel</h2>
                @if($hotel->description)
                <p>{{ $hotel->description }}</p>
                @else
                <p>Experience world-class hospitality at {{ $hotel->name }}. Our hotel offers premium rooms, fine dining, spa facilities, and exceptional service to make your stay truly memorable.</p>
                @endif

                @if($hotel->email || $hotel->phone)
                <div style="margin-top: 24px; padding: 20px; border: 1px solid #dce4df; background: #fff;">
                    <h4 style="margin: 0 0 12px; font-size: 16px;">Contact Information</h4>
                    @if($hotel->email)
                        <p style="margin: 0 0 8px; color: #66716b; font-size: 14px;"><i class="fas fa-envelope" style="color: #bd8c3a; margin-right: 8px;"></i> {{ $hotel->email }}</p>
                    @endif
                    @if($hotel->phone)
                        <p style="margin: 0 0 8px; color: #66716b; font-size: 14px;"><i class="fas fa-phone" style="color: #bd8c3a; margin-right: 8px;"></i> {{ $hotel->phone }}</p>
                    @endif
                    @if($hotel->address)
                        <p style="margin: 0; color: #66716b; font-size: 14px;"><i class="fas fa-map-marker-alt" style="color: #bd8c3a; margin-right: 8px;"></i> {{ $hotel->address }}</p>
                    @endif
                </div>
                @endif

                <div style="margin-top: 36px;">
                    <h2>Available Room Types</h2>
                    @if($roomTypes->count())
                        <div class="room-list" style="margin-top: 18px;">
                            @foreach($roomTypes as $rt)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 18px; border: 1px solid #dce4df; background: #fff;">
                                <div>
                                    <h4 style="margin: 0 0 4px; font-size: 17px;">{{ $rt->name }}</h4>
                                    <span style="color: #66716b; font-size: 13px;">Max {{ $rt->max_occupancy }} guests</span>
                                </div>
                                <div style="text-align: right;">
                                    <strong style="font-size: 22px;">{{ $currencySymbol }}{{ number_format($rt->base_rate, 0) }}</strong>
                                    <span style="color: #66716b; font-size: 12px; display: block;">/ night</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color: #66716b;">Room information coming soon.</p>
                    @endif
                </div>
            </div>

            <div class="hotel-detail__sidebar">
                <div class="booking-widget">
                    <h3>Book This Hotel</h3>
                    <form action="{{ route('web.booking') }}" method="GET">
                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                        <label>
                            <span>Check-in</span>
                            <input type="date" name="check_in" required id="hotel-checkin">
                        </label>
                        <label>
                            <span>Check-out</span>
                            <input type="date" name="check_out" required id="hotel-checkout">
                        </label>
                        <label>
                            <span>Guests</span>
                            <select name="adults">
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                            </select>
                        </label>
                        <button type="submit"><i class="fas fa-search"></i> Check Availability</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <h2>Ready to Book {{ $hotel->name }}?</h2>
        <p>Get the best rates when you book directly through our website.</p>
        <a href="{{ route('web.booking') }}?hotel_id={{ $hotel->id }}" class="btn-primary-site" style="background: #fff; color: #14624f;">
            <i class="fas fa-calendar-check"></i> Book Now
        </a>
    </section>
</main>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var today = new Date();
    var tomorrow = new Date(today.getTime() + 86400000);
    var nextDay = new Date(today.getTime() + 172800000);
    function iso(d) { return d.toISOString().slice(0, 10); }
    var ci = document.getElementById('hotel-checkin');
    var co = document.getElementById('hotel-checkout');
    if (ci) { ci.min = iso(today); ci.value = iso(tomorrow); }
    if (co) { co.min = iso(tomorrow); co.value = iso(nextDay); }
});
</script>
@endsection
