@extends('web.layouts.app')

@section('title', 'Book Now - ' . ($company->name ?? config('app.name')))

@section('content')
<main class="booking-page">
    <section class="booking-hero" style="min-height: 380px;">
        <div class="booking-hero__media" style="background-image: url('{{ asset('assets/admin/img/backgrounds/12.jpg') }}');"></div>
        <div class="booking-hero__overlay"></div>
        <div class="booking-shell booking-hero__content" style="min-height: 380px; align-items: center;">
            <div class="booking-hero__copy">
                <p class="booking-kicker">Direct Booking Engine</p>
                <h1>Reserve Your Room</h1>
                <p>Search real-time availability and book directly for the best rates.</p>
            </div>
        </div>
    </section>

    <section class="section-shell" style="padding-top: 40px; padding-bottom: 60px;">
        <form id="booking-search-form" class="booking-search__form" style="margin-bottom: 32px;">
            <label>
                <span>Hotel</span>
                <select id="bk-hotel" name="hotel_id" required>
                    <option value="">Select hotel</option>
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}" {{ request('hotel_id') == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Check-in</span>
                <input type="date" id="bk-checkin" name="check_in" required>
            </label>
            <label>
                <span>Check-out</span>
                <input type="date" id="bk-checkout" name="check_out" required>
            </label>
            <label>
                <span>Guests</span>
                <input type="number" id="bk-adults" name="adults" min="1" max="8" value="2" required>
            </label>
            <button type="submit">Search Availability</button>
        </form>

        <div id="bk-message" class="booking-search__message" role="status">Select a hotel and dates to check availability.</div>

        <div class="booking-layout" id="bk-results" style="display: none;">
            <div class="booking-main">
                <div class="booking-section-heading">
                    <div>
                        <p>Available Rooms</p>
                        <h2 id="bk-count">0 rooms found</h2>
                    </div>
                </div>

                <div class="room-grid" id="bk-room-grid"></div>

                <div id="bk-no-rooms" style="display: none; text-align: center; padding: 48px 0;">
                    <i class="fas fa-bed" style="font-size: 48px; color: #dce4df; margin-bottom: 12px;"></i>
                    <h3>No rooms available</h3>
                    <p style="color: #66716b;">Try different dates or select another hotel.</p>
                </div>
            </div>

            <aside class="booking-summary" aria-label="Reservation summary">
                <div class="booking-summary__header">
                    <p>Your Selection</p>
                    <h2 id="summary-room">--</h2>
                    <span id="summary-type">Select a room</span>
                </div>

                <dl class="booking-summary__lines">
                    <div>
                        <dt>Check-in</dt>
                        <dd id="summary-checkin">--</dd>
                    </div>
                    <div>
                        <dt>Check-out</dt>
                        <dd id="summary-checkout">--</dd>
                    </div>
                    <div>
                        <dt>Nights</dt>
                        <dd id="summary-nights">0</dd>
                    </div>
                    <div>
                        <dt>Guests</dt>
                        <dd id="summary-guests">2</dd>
                    </div>
                    <div>
                        <dt>Rate / night</dt>
                        <dd id="summary-rate">{{ $currencySymbol }}0</dd>
                    </div>
                    <div>
                        <dt>Taxes (<span id="summary-tax-pct">{{ $taxRate }}</span>%)</dt>
                        <dd id="summary-tax">{{ $currencySymbol }}0</dd>
                    </div>
                </dl>

                <div class="booking-summary__total">
                    <span>Total</span>
                    <strong id="summary-total">{{ $currencySymbol }}0</strong>
                </div>

                <button id="bk-proceed" class="btn-primary-site" style="width: 100%; justify-content: center; margin-top: 18px; display: none;" disabled>
                    <i class="fas fa-arrow-right"></i> Proceed to Checkout
                </button>
            </aside>
        </div>
    </section>
</main>
@endsection

@section('script')
<script>
(function() {
    var CURRENCY = '{{ $currencySymbol }}';
    var TAX_RATE = {{ $taxRate }};
    var selectedRoom = null;

    function byId(id) { return document.getElementById(id); }
    function money(v) { return CURRENCY + Number(v).toLocaleString('en-US'); }

    function diffNights(a, b) {
        var d1 = new Date(a), d2 = new Date(b);
        return Math.max(1, Math.round((d2 - d1) / 86400000));
    }

    function setDefaultDates() {
        var params = new URLSearchParams(window.location.search);
        var ci = byId('bk-checkin'), co = byId('bk-checkout'), hotel = byId('bk-hotel'), adults = byId('bk-adults');
        var urlCI = params.get('check_in');
        var urlCO = params.get('check_out');
        var urlAdults = params.get('adults');

        var t = new Date(), tw = new Date(t.getTime() + 86400000), nw = new Date(t.getTime() + 172800000);
        function iso(d) { return d.toISOString().slice(0, 10); }

        if (ci) { ci.min = iso(t); ci.value = urlCI || iso(tw); }
        if (co) { co.min = iso(tw); co.value = urlCO || iso(nw); }
        if (hotel && params.get('hotel_id')) { hotel.value = params.get('hotel_id'); }
        if (adults && urlAdults) { adults.value = urlAdults; }

        if (hotel && hotel.value && ci.value && co.value) {
            byId('booking-search-form').dispatchEvent(new Event('submit'));
        }
    }

    function updateSummary() {
        if (!selectedRoom) return;
        var ci = byId('bk-checkin').value;
        var co = byId('bk-checkout').value;
        var adults = Number(byId('bk-adults').value || 2);
        var nights = diffNights(ci, co);
        var tax = Math.round(selectedRoom.rate_per_night * nights * TAX_RATE / 100);
        var total = selectedRoom.rate_per_night * nights + tax;

        byId('summary-room').textContent = selectedRoom.room_number + ' - ' + selectedRoom.type_name;
        byId('summary-type').textContent = selectedRoom.bed_type || 'Standard';
        byId('summary-checkin').textContent = ci;
        byId('summary-checkout').textContent = co;
        byId('summary-nights').textContent = nights;
        byId('summary-guests').textContent = adults;
        byId('summary-rate').textContent = money(selectedRoom.rate_per_night);
        byId('summary-tax').textContent = money(tax);
        byId('summary-total').textContent = money(total);
    }

    function renderRooms(rooms) {
        var grid = byId('bk-room-grid');
        var noRooms = byId('bk-no-rooms');
        var results = byId('bk-results');
        results.style.display = 'grid';
        grid.innerHTML = '';

        if (rooms.length === 0) {
            noRooms.style.display = 'block';
            byId('bk-count').textContent = '0 rooms found';
            byId('bk-proceed').style.display = 'none';
            return;
        }

        noRooms.style.display = 'none';
        byId('bk-count').textContent = rooms.length + ' room' + (rooms.length > 1 ? 's' : '') + ' found';
        byId('bk-proceed').style.display = 'flex';
        byId('bk-proceed').disabled = false;

        rooms.forEach(function(room, i) {
            var article = document.createElement('article');
            article.className = 'room-card' + (i === 0 ? ' is-selected' : '');
            article.setAttribute('data-room-id', room.id);
            article.innerHTML =
                '<div class="room-card__body" style="grid-column: 1 / -1;">' +
                    '<div>' +
                        '<p>' + room.type_name + '</p>' +
                        '<h3>Room ' + room.room_number + '</h3>' +
                        '<span>' + room.bed_type + ' &middot; Up to ' + room.max_occupancy + ' guests</span>' +
                    '</div>' +
                    '<div class="room-card__footer">' +
                        '<strong>' + money(room.rate_per_night) + '</strong>' +
                        '<button type="button" data-select-room>' + (i === 0 ? 'Selected' : 'Select') + '</button>' +
                    '</div>' +
                '</div>';
            grid.appendChild(article);

            article.querySelector('[data-select-room]').addEventListener('click', function() {
                document.querySelectorAll('.room-card').forEach(function(c) {
                    c.classList.remove('is-selected');
                    c.querySelector('[data-select-room]').textContent = 'Select';
                });
                article.classList.add('is-selected');
                article.querySelector('[data-select-room]').textContent = 'Selected';
                selectedRoom = room;
                updateSummary();
                byId('bk-proceed').disabled = false;
            });

            if (i === 0) {
                selectedRoom = room;
                updateSummary();
                byId('bk-proceed').disabled = false;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setDefaultDates();

        byId('booking-search-form').addEventListener('submit', function(e) {
            e.preventDefault();

            var hotelId = byId('bk-hotel').value;
            var checkin = byId('bk-checkin').value;
            var checkout = byId('bk-checkout').value;
            var adults = byId('bk-adults').value;
            var msg = byId('bk-message');

            if (!hotelId) { msg.textContent = 'Please select a hotel.'; msg.className = 'booking-search__message is-error'; return; }
            if (!checkin || !checkout) { msg.textContent = 'Please select check-in and check-out dates.'; msg.className = 'booking-search__message is-error'; return; }
            if (new Date(checkout) <= new Date(checkin)) { msg.textContent = 'Check-out must be after check-in.'; msg.className = 'booking-search__message is-error'; return; }

            msg.textContent = 'Searching for available rooms...';
            msg.className = 'booking-search__message';

            fetch('{{ route("web.booking.availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ hotel_id: hotelId, check_in: checkin, check_out: checkout, adults: adults })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    if (data.tax_rate) { TAX_RATE = data.tax_rate; }
                    msg.textContent = data.rooms.length + ' room(s) available for ' + data.nights + ' night(s) at direct booking rates.';
                    msg.className = 'booking-search__message is-success';
                    selectedRoom = null;
                    renderRooms(data.rooms);
                } else {
                    msg.textContent = data.message || 'No rooms available for these dates.';
                    msg.className = 'booking-search__message is-error';
                }
            })
            .catch(function() {
                msg.textContent = 'Something went wrong. Please try again.';
                msg.className = 'booking-search__message is-error';
            });
        });

        byId('bk-proceed').addEventListener('click', function() {
            if (!selectedRoom) return;
            var params = new URLSearchParams({
                hotel_id: byId('bk-hotel').value,
                room_id: selectedRoom.id,
                check_in: byId('bk-checkin').value,
                check_out: byId('bk-checkout').value,
                adults: byId('bk-adults').value
            });
            window.location.href = '{{ route("web.booking.checkout") }}?' + params.toString();
        });
    });
})();
</script>
@endsection
