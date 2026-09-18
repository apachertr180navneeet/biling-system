@extends('web.layouts.app')

@section('style')
<link href="{{asset('assets/web/css/digine.css')}}" rel="stylesheet">
@endsection

@section('content')
<main class="booking-engine" data-base-rate="142" data-currency-symbol="{{ $currencySymbol ?? '$' }}">
    <section class="booking-hero">
        <div class="booking-hero__media" style="background-image: url('{{ asset('assets/admin/img/backgrounds/18.jpg') }}');"></div>
        <div class="booking-hero__overlay"></div>
        <div class="booking-shell booking-hero__content">
            <div class="booking-hero__copy">
                <p class="booking-kicker">Official direct booking</p>
                <h1>Mehmaan stays, booked at the source</h1>
                <p>Reserve rooms, compare live rates, and confirm guest details in one focused booking flow.</p>
            </div>
            <div class="booking-trust">
                <span>Best rate guarantee</span>
                <span>No third-party fees</span>
                <span>Instant reservation hold</span>
            </div>
        </div>
    </section>

    <section class="booking-search" aria-label="Search availability">
        <div class="booking-shell">
            <form class="booking-search__form" id="booking-search-form">
                <label>
                    <span>Check-in</span>
                    <input type="date" id="checkin" name="checkin" required>
                </label>
                <label>
                    <span>Check-out</span>
                    <input type="date" id="checkout" name="checkout" required>
                </label>
                <label>
                    <span>Guests</span>
                    <input type="number" id="guests" name="guests" min="1" max="8" value="2" required>
                </label>
                <label>
                    <span>Rooms</span>
                    <select id="rooms" name="rooms">
                        <option value="1">1 room</option>
                        <option value="2">2 rooms</option>
                        <option value="3">3 rooms</option>
                    </select>
                </label>
                <button type="submit">Check Availability</button>
            </form>
            <div class="booking-search__message" id="availability-result" role="status">Select dates to review direct booking rates.</div>
        </div>
    </section>

    <section class="booking-shell booking-layout">
        <div class="booking-main">
            <div class="booking-section-heading">
                <p>Available stays</p>
                <h2>Choose a reservation</h2>
            </div>

            <div class="room-grid" id="room-grid">
                @php
                    $reservations = [
                        ['name' => 'Courtyard King', 'type' => 'King Room', 'rate' => 142, 'img' => '1.jpg', 'tag' => 'Best value', 'meta' => 'Garden view, breakfast included'],
                        ['name' => 'City Twin', 'type' => 'Twin Room', 'rate' => 156, 'img' => '2.jpg', 'tag' => 'Flexible', 'meta' => 'Two beds, free cancellation'],
                        ['name' => 'Poolside Deluxe', 'type' => 'Deluxe Room', 'rate' => 184, 'img' => '3.jpg', 'tag' => 'Popular', 'meta' => 'Balcony, pool access'],
                        ['name' => 'Heritage Suite', 'type' => 'Suite', 'rate' => 238, 'img' => '4.jpg', 'tag' => 'Upgrade', 'meta' => 'Living area, evening lounge'],
                        ['name' => 'Family Studio', 'type' => 'Family Room', 'rate' => 212, 'img' => '5.jpg', 'tag' => 'Sleeps 4', 'meta' => 'Kitchenette, extra sofa bed'],
                        ['name' => 'Executive Club', 'type' => 'Club Room', 'rate' => 196, 'img' => '7.jpg', 'tag' => 'Work ready', 'meta' => 'Desk, club floor benefits'],
                        ['name' => 'Terrace Premier', 'type' => 'Premier Room', 'rate' => 226, 'img' => '11.jpg', 'tag' => 'Limited', 'meta' => 'Private terrace, late checkout'],
                        ['name' => 'Presidential Suite', 'type' => 'Signature Suite', 'rate' => 410, 'img' => '12.jpg', 'tag' => 'Signature', 'meta' => 'Dining room, airport transfer'],
                    ];
                @endphp

                @foreach($reservations as $index => $reservation)
                    <article class="room-card {{ $index === 0 ? 'is-selected' : '' }}" data-room-card data-name="{{ $reservation['name'] }}" data-type="{{ $reservation['type'] }}" data-rate="{{ $reservation['rate'] }}">
                        <div class="room-card__image" style="background-image: url('{{ asset('assets/admin/img/elements/' . $reservation['img']) }}');">
                            <span>{{ $reservation['tag'] }}</span>
                        </div>
                        <div class="room-card__body">
                            <div>
                                <p>{{ $reservation['type'] }}</p>
                                <h3>{{ $reservation['name'] }}</h3>
                                <span>{{ $reservation['meta'] }}</span>
                            </div>
                            <div class="room-card__footer">
                                <strong>{{ $currencySymbol ?? '$' }}{{ $reservation['rate'] }}</strong>
                                <button type="button" data-select-room>{{ $index === 0 ? 'Selected' : 'Select' }}</button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <aside class="booking-summary" aria-label="Reservation summary">
            <div class="booking-summary__header">
                <p>Your direct rate</p>
                <h2 id="summary-room">Courtyard King</h2>
                <span id="summary-type">King Room</span>
            </div>

            <dl class="booking-summary__lines">
                <div>
                    <dt>Nights</dt>
                    <dd id="summary-nights">1</dd>
                </div>
                <div>
                    <dt>Rooms</dt>
                    <dd id="summary-rooms">1</dd>
                </div>
                <div>
                    <dt>Guests</dt>
                    <dd id="summary-guests">2</dd>
                </div>
                <div>
                    <dt>Taxes and fees</dt>
                    <dd id="summary-tax">{{ $currencySymbol ?? '$' }}18</dd>
                </div>
            </dl>

            <div class="booking-summary__total">
                <span>Total</span>
                <strong id="summary-total">{{ $currencySymbol ?? '$' }}160</strong>
            </div>

            <form class="guest-form" id="guest-form">
                <label>
                    <span>Guest name</span>
                    <input id="guest-name" type="text" placeholder="Full name" required>
                </label>
                <label>
                    <span>Email</span>
                    <input id="guest-email" type="email" placeholder="you@example.com" required>
                </label>
                <label>
                    <span>Mobile</span>
                    <input id="guest-phone" type="tel" placeholder="+1 555 0100">
                </label>
                <button type="submit">Reserve Direct</button>
            </form>

            <div class="booking-confirmation" id="booking-result" aria-live="polite"></div>
        </aside>
    </section>
</main>
@endsection

@section('script')
<script src="{{asset('assets/web/js/digine.js')}}"></script>
@endsection
