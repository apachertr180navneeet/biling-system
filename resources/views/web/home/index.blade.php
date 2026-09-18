@extends('web.layouts.app')

@section('title', 'Home - ' . ($company->name ?? config('app.name')))
@section('meta_description', $company->tagline ?? 'Premium hotel management and direct booking engine. Search rooms, compare rates, and book directly for the best prices.')

@section('content')
<main>
    <section class="booking-hero">
        <div class="booking-hero__media" style="background-image: url('{{ asset('assets/admin/img/backgrounds/18.jpg') }}');"></div>
        <div class="booking-hero__overlay"></div>
        <div class="booking-shell booking-hero__content">
            <div class="booking-hero__copy">
                <p class="booking-kicker">Welcome to {{ $company->name ?? config('app.name') }}</p>
                <h1>Find Your Perfect Stay</h1>
                <p>Search availability, compare live room rates, and book directly for the best prices. No middlemen, no hidden fees.</p>
            </div>
            <div class="booking-trust" aria-label="Booking highlights">
                <span><i class="fas fa-check-circle"></i> Best Direct Rates</span>
                <span><i class="fas fa-check-circle"></i> Instant Confirmation</span>
                <span><i class="fas fa-check-circle"></i> Free Cancellation</span>
            </div>
        </div>
    </section>

    <section class="booking-search" aria-label="Quick search">
        <div class="booking-shell">
            <form class="booking-search__form" id="home-search-form" action="{{ route('web.booking') }}" method="GET">
                <label>
                    <span>Hotel</span>
                    <select name="hotel_id" id="home-hotel">
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Check-in</span>
                    <input type="date" name="check_in" id="home-checkin" required>
                </label>
                <label>
                    <span>Check-out</span>
                    <input type="date" name="check_out" id="home-checkout" required>
                </label>
                <label>
                    <span>Guests</span>
                    <input type="number" name="adults" min="1" max="8" value="2">
                </label>
                <button type="submit">Search Rooms</button>
            </form>
        </div>
    </section>

    <section class="stats-bar">
        <div class="stats-bar__inner">
            <div class="stats-bar__item">
                <strong>{{ $hotels->count() }}</strong>
                <span>Hotels</span>
            </div>
            <div class="stats-bar__item">
                <strong>{{ $totalRooms }}</strong>
                <span>Rooms</span>
            </div>
            <div class="stats-bar__item">
                <strong>{{ $distinctCities ?: '-' }}</strong>
                <span>Destinations</span>
            </div>
            <div class="stats-bar__item">
                <strong>{{ number_format($hotels->count() * 100) }}+</strong>
                <span>Happy Guests</span>
            </div>
        </div>
    </section>

    @if($hotels->count())
    <section class="section-pad" style="background: #fff;">
        <div class="section-shell">
            <div class="section-header">
                <p class="kicker">Our Properties</p>
                <h2>Featured Hotels</h2>
                <p>Handpicked properties with world-class amenities and exceptional service.</p>
            </div>

            <div class="hotel-grid">
                @foreach($hotels->take(6) as $hotel)
                <article class="hotel-card">
                    <div class="hotel-card__image" style="background-image: url('{{ asset('assets/admin/img/backgrounds/' . (($loop->index % 18) + 1) . '.jpg') }}');">
                        <span class="hotel-card__badge">{{ $hotel->rooms_count }} Rooms</span>
                    </div>
                    <div class="hotel-card__body">
                        <h3>{{ $hotel->name }}</h3>
                        @if($hotel->city || $hotel->country)
                        <p class="location"><i class="fas fa-map-marker-alt"></i> {{ collect([$hotel->city, $hotel->country])->filter()->implode(', ') }}</p>
                        @endif
                        @if($hotel->description)
                        <p class="desc">{{ Str::limit($hotel->description, 120) }}</p>
                        @endif
                        <div class="hotel-card__footer">
                            @php
                                $firstRoom = $hotel->rooms->first();
                                $baseRate = $firstRoom?->roomType?->base_rate;
                            @endphp
                            @if($baseRate)
                            <span class="rate">From <strong>{{ $currencySymbol }}{{ number_format($baseRate, 0) }}</strong>/night</span>
                            @endif
                            <div class="hotel-card__actions">
                                <a href="{{ route('web.hotel.show', $hotel->slug) }}">View Details</a>
                                <a href="{{ route('web.booking') }}?hotel_id={{ $hotel->id }}" class="hotel-card__book">Book Now</a>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($homeFeatures->count())
    <section class="section-pad">
        <div class="section-shell">
            <div class="section-header">
                <p class="kicker">Why Choose Us</p>
                <h2>Every Stay, Made Special</h2>
                <p>We combine modern technology with personalized hospitality to deliver an unmatched guest experience.</p>
            </div>

            <div class="features-grid">
                @foreach($homeFeatures as $feature)
                <div class="feature-card">
                    <div class="icon"><i class="fas {{ $feature->icon ?? 'fa-star' }}"></i></div>
                    <h3>{{ $feature->title }}</h3>
                    <p>{{ $feature->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($testimonials->count())
    <section class="section-pad" style="background: #fff;">
        <div class="section-shell">
            <div class="section-header">
                <p class="kicker">Guest Reviews</p>
                <h2>What Our Guests Say</h2>
                <p>Real feedback from real guests who experienced our hospitality.</p>
            </div>

            <div class="testimonial-grid">
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $testimonial->rating)
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $testimonial->rating)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <blockquote>"{{ $testimonial->quote }}"</blockquote>
                    <div class="author">
                        <div class="author-avatar">{{ strtoupper(substr($testimonial->name, 0, 2)) }}</div>
                        <div>
                            <div class="author-name">{{ $testimonial->name }}</div>
                            @if($testimonial->role)
                            <div class="author-title">{{ $testimonial->role }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="cta-band">
        <h2>Ready to Book Your Stay?</h2>
        <p>Get the best rates when you book directly. No hidden charges, instant confirmation.</p>
        <a href="{{ route('web.booking') }}" class="btn-primary-site" style="background: #fff; color: #14624f;">
            <i class="fas fa-search"></i> Search & Book Now
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

    var checkin = document.getElementById('home-checkin');
    var checkout = document.getElementById('home-checkout');
    if (checkin) { checkin.min = iso(today); checkin.value = iso(tomorrow); }
    if (checkout) { checkout.min = iso(tomorrow); checkout.value = iso(nextDay); }
});
</script>
@endsection
