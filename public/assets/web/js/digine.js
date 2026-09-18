(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function currencySymbol() {
        var engine = document.querySelector('.booking-engine');
        return engine && engine.dataset.currencySymbol ? engine.dataset.currencySymbol : '$';
    }

    function money(value) {
        return currencySymbol() + value.toLocaleString('en-US');
    }

    function parseDate(value) {
        if (!value) {
            return null;
        }

        var parts = value.split('-').map(Number);
        return new Date(parts[0], parts[1] - 1, parts[2]);
    }

    function diffNights(checkin, checkout) {
        var start = parseDate(checkin);
        var end = parseDate(checkout);

        if (!start || !end || end <= start) {
            return 1;
        }

        return Math.round((end - start) / 86400000);
    }

    function setMessage(message, state) {
        var el = byId('availability-result');
        if (!el) {
            return;
        }

        el.textContent = message;
        el.className = 'booking-search__message' + (state ? ' is-' + state : '');
    }

    function selectedCard() {
        return document.querySelector('[data-room-card].is-selected') || document.querySelector('[data-room-card]');
    }

    function state() {
        var card = selectedCard();
        var checkin = byId('checkin').value;
        var checkout = byId('checkout').value;
        var rooms = Number(byId('rooms').value || 1);
        var guests = Number(byId('guests').value || 1);
        var nights = diffNights(checkin, checkout);
        var rate = Number(card.dataset.rate || 0);
        var subtotal = rate * nights * rooms;
        var tax = Math.round(subtotal * 0.12);

        return {
            card: card,
            rooms: rooms,
            guests: guests,
            nights: nights,
            rate: rate,
            tax: tax,
            total: subtotal + tax
        };
    }

    function updateSummary() {
        var current = state();

        byId('summary-room').textContent = current.card.dataset.name;
        byId('summary-type').textContent = current.card.dataset.type;
        byId('summary-nights').textContent = current.nights;
        byId('summary-rooms').textContent = current.rooms;
        byId('summary-guests').textContent = current.guests;
        byId('summary-tax').textContent = money(current.tax);
        byId('summary-total').textContent = money(current.total);
    }

    function setDefaultDates() {
        var checkin = byId('checkin');
        var checkout = byId('checkout');
        var today = new Date();
        var tomorrow = new Date(today.getTime() + 86400000);
        var nextDay = new Date(today.getTime() + 172800000);

        function iso(date) {
            return date.toISOString().slice(0, 10);
        }

        checkin.min = iso(today);
        checkout.min = iso(tomorrow);
        checkin.value = iso(tomorrow);
        checkout.value = iso(nextDay);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var searchForm = byId('booking-search-form');
        var guestForm = byId('guest-form');
        var result = byId('booking-result');

        setDefaultDates();
        updateSummary();

        document.querySelectorAll('[data-select-room]').forEach(function (button) {
            button.addEventListener('click', function () {
                document.querySelectorAll('[data-room-card]').forEach(function (card) {
                    card.classList.remove('is-selected');
                    card.querySelector('[data-select-room]').textContent = 'Select';
                });

                button.closest('[data-room-card]').classList.add('is-selected');
                button.textContent = 'Selected';
                result.className = 'booking-confirmation';
                result.textContent = '';
                updateSummary();
            });
        });

        ['checkin', 'checkout', 'guests', 'rooms'].forEach(function (fieldId) {
            byId(fieldId).addEventListener('change', updateSummary);
        });

        searchForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var start = parseDate(byId('checkin').value);
            var end = parseDate(byId('checkout').value);

            if (!start || !end) {
                setMessage('Choose check-in and check-out dates to continue.', 'error');
                return;
            }

            if (end <= start) {
                setMessage('Check-out must be after check-in.', 'error');
                return;
            }

            updateSummary();
            setMessage(document.querySelectorAll('[data-room-card]').length + ' reservation options are available at direct booking rates.', 'success');
        });

        guestForm.addEventListener('submit', function (event) {
            event.preventDefault();

            if (!byId('guest-name').value.trim() || !byId('guest-email').value.trim()) {
                result.textContent = 'Enter guest name and email before reserving.';
                result.className = 'booking-confirmation is-visible is-error';
                return;
            }

            var current = state();
            var reference = 'DIR' + String(Math.floor(100000 + Math.random() * 900000));
            result.textContent = current.card.dataset.name + ' is held for ' + current.nights + ' night(s). Reference ' + reference + '. Total due at confirmation: ' + money(current.total) + '.';
            result.className = 'booking-confirmation is-visible';
        });
    });
})();
