@extends('web.layouts.app')

@section('title', 'Our Hotels - ' . ($company->name ?? config('app.name')))

@section('content')
<main>
    <section class="page-hero">
        <div class="page-hero__bg" style="background-image: url('{{ asset('assets/admin/img/backgrounds/5.jpg') }}');"></div>
        <div class="page-hero__overlay"></div>
        <div class="page-hero__content">
            <p class="kicker">Our Properties</p>
            <h1>Explore Our Hotels</h1>
            <p>Discover our collection of premium hotels across India.</p>
        </div>
    </section>

    <section class="section-pad">
        <div class="section-shell">
            @if($hotels->count())
                <div class="hotel-grid">
                    @foreach($hotels as $hotel)
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
            @else
                <div style="text-align: center; padding: 60px 0;">
                    <i class="fas fa-hotel" style="font-size: 48px; color: #dce4df; margin-bottom: 16px;"></i>
                    <h3>No hotels available yet</h3>
                    <p style="color: #66716b;">Check back soon for our upcoming properties.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="cta-band">
        <h2>Can't Find What You're Looking For?</h2>
        <p>Contact us directly and we'll help you find the perfect accommodation.</p>
        <a href="{{ route('web.contact') }}" class="btn-primary-site" style="background: #fff; color: #14624f;">
            <i class="fas fa-envelope"></i> Get in Touch
        </a>
    </section>
</main>
@endsection
