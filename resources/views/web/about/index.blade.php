@extends('web.layouts.app')

@section('title', 'About Us - ' . ($company->name ?? config('app.name')))

@section('content')
<main>
    <section class="page-hero">
        <div class="page-hero__bg" style="background-image: url('{{ asset('assets/admin/img/backgrounds/8.jpg') }}');"></div>
        <div class="page-hero__overlay"></div>
        <div class="page-hero__content">
            <p class="kicker">About Us</p>
            <h1>Our Story</h1>
            <p>Discover the passion behind {{ $company->name ?? config('app.name') }} and our commitment to hospitality.</p>
        </div>
    </section>

    <section class="section-pad">
        <div class="section-shell">
            <div class="about-content">
                <div class="about-content__image" style="background-image: url('{{ asset('assets/admin/img/backgrounds/3.jpg') }}');"></div>
                <div class="about-content__text">
                    @if($company?->founding_year)
                    <h3>Crafting Unforgettable Experiences Since {{ $company->founding_year }}</h3>
                    @else
                    <h3>Crafting Unforgettable Experiences</h3>
                    @endif
                    @if($company?->about)
                        {!! nl2br(e($company->about)) !!}
                    @else
                    <p>{{ $company->name ?? config('app.name') }} was founded with a simple mission: to combine modern technology with genuine hospitality to create stays that guests will cherish forever.</p>
                    <p>Our integrated management platform ensures every detail of your stay is handled with precision, from the moment you book online to the moment you check out.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($milestones->count())
    <section class="section-pad" style="background: #fff;">
        <div class="section-shell">
            <div class="section-header">
                <p class="kicker">Our Journey</p>
                <h2>Key Milestones</h2>
            </div>

            <div style="max-width: 700px; margin: 0 auto;">
                <div class="timeline">
                    @foreach($milestones as $milestone)
                    <div class="timeline__item">
                        <strong>{{ $milestone->year }}</strong>
                        <h4>{{ $milestone->title }}</h4>
                        @if($milestone->description)
                        <p>{{ $milestone->description }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($values->count())
    <section class="section-pad">
        <div class="section-shell">
            <div class="section-header">
                <p class="kicker">Our Values</p>
                <h2>What Drives Us</h2>
            </div>

            <div class="features-grid">
                @foreach($values as $value)
                <div class="feature-card">
                    <div class="icon"><i class="fas {{ $value->icon ?? 'fa-star' }}"></i></div>
                    <h3>{{ $value->title }}</h3>
                    <p>{{ $value->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="cta-band">
        <h2>Experience {{ $company->name ?? config('app.name') }}</h2>
        <p>Book your next stay directly and enjoy exclusive rates and perks.</p>
        <a href="{{ route('web.booking') }}" class="btn-primary-site" style="background: #fff; color: #14624f;">
            <i class="fas fa-calendar-check"></i> Book Now
        </a>
    </section>
</main>
@endsection
