@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-calendar-event"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($event) ? 'Edit Event' : 'Book Event' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Banquet</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.banquet.events.index') }}">Events</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($event) ? 'Edit' : 'Book' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.banquet.events.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <form action="{{ isset($event) ? route('admin.banquet.events.update', $event) : route('admin.banquet.events.store') }}" method="POST">
        @csrf
        @if(isset($event)) @method('PUT') @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="m-section-divider">Event Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" id="hotelSelect" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $event?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hall <span class="text-danger">*</span></label>
                        <select name="hall_id" class="form-select" id="hallSelect" required>
                            <option value="">Select Hall</option>
                            @foreach($halls as $hall)
                            <option value="{{ $hall->id }}" data-price="{{ $hall->base_price }}" data-capacity="{{ $hall->capacity }}" {{ old('hall_id', $event?->hall_id) == $hall->id ? 'selected' : '' }}>{{ $hall->name }} (Capacity: {{ $hall->capacity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Event Type <span class="text-danger">*</span></label>
                        <select name="event_type" class="form-select" required>
                            <option value="wedding" {{ old('event_type', $event?->event_type) == 'wedding' ? 'selected' : '' }}>Wedding</option>
                            <option value="conference" {{ old('event_type', $event?->event_type) == 'conference' ? 'selected' : '' }}>Conference</option>
                            <option value="seminar" {{ old('event_type', $event?->event_type) == 'seminar' ? 'selected' : '' }}>Seminar</option>
                            <option value="exhibition" {{ old('event_type', $event?->event_type) == 'exhibition' ? 'selected' : '' }}>Exhibition</option>
                            <option value="corporate" {{ old('event_type', $event?->event_type) == 'corporate' ? 'selected' : '' }}>Corporate</option>
                            <option value="social" {{ old('event_type', $event?->event_type) == 'social' ? 'selected' : '' }}>Social</option>
                            <option value="birthday" {{ old('event_type', $event?->event_type) == 'birthday' ? 'selected' : '' }}>Birthday</option>
                            <option value="anniversary" {{ old('event_type', $event?->event_type) == 'anniversary' ? 'selected' : '' }}>Anniversary</option>
                            <option value="other" {{ old('event_type', $event?->event_type ?? 'other') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Event Name <span class="text-danger">*</span></label>
                        <input type="text" name="event_name" class="form-control" value="{{ old('event_name', $event?->event_name) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Event Date <span class="text-danger">*</span></label>
                        <input type="date" name="event_date" class="form-control" value="{{ old('event_date', $event?->event_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Expected Guests <span class="text-danger">*</span></label>
                        <input type="number" name="expected_guests" class="form-control" value="{{ old('expected_guests', $event?->expected_guests) }}" required min="1">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $event?->start_time) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">End Time <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $event?->end_time) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Booking Status <span class="text-danger">*</span></label>
                        <select name="booking_status" class="form-select" required>
                            <option value="inquiry" {{ old('booking_status', $event?->booking_status ?? 'inquiry') == 'inquiry' ? 'selected' : '' }}>Inquiry</option>
                            <option value="proposed" {{ old('booking_status', $event?->booking_status) == 'proposed' ? 'selected' : '' }}>Proposed</option>
                            <option value="confirmed" {{ old('booking_status', $event?->booking_status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_progress" {{ old('booking_status', $event?->booking_status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('booking_status', $event?->booking_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('booking_status', $event?->booking_status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Guest</label>
                        <select name="guest_id" class="form-select">
                            <option value="">Select Guest (Optional)</option>
                            @foreach($guests as $guest)
                            <option value="{{ $guest->id }}" {{ old('guest_id', $event?->guest_id) == $guest->id ? 'selected' : '' }}>{{ $guest->first_name }} {{ $guest->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="m-section-divider">Contact Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                        <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $event?->contact_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Phone <span class="text-danger">*</span></label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $event?->contact_phone) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $event?->contact_email) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="m-section-divider">Services</div>
                <div id="services-container">
                    @if(isset($event) && $event->services->count() > 0)
                    @foreach($event->services as $idx => $svc)
                    <div class="row service-row mb-2">
                        <div class="col-md-5">
                            <select name="services[{{ $idx }}][id]" class="form-select service-select">
                                <option value="">Select Service</option>
                                @foreach($services as $s)
                                <option value="{{ $s->id }}" data-price="{{ $s->unit_price }}" {{ $s->id == $svc->id ? 'selected' : '' }}>{{ $s->name }} (₹{{ $s->unit_price }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="services[{{ $idx }}][quantity]" class="form-control service-qty" value="{{ $svc->pivot->quantity }}" min="1">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control service-total" readonly value="₹{{ number_format($svc->pivot->total_price, 2) }}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-service"><i class="bx bx-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
                <button type="button" id="add-service" class="btn btn-outline-primary btn-sm mt-2"><i class="bx bx-plus"></i> Add Service</button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="m-section-divider">Charges & Payment</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Hall Charges <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="hall_charges" class="form-control" id="hallCharges" value="{{ old('hall_charges', $event?->hall_charges) }}" required min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Additional Charges</label>
                        <input type="number" step="0.01" name="additional_charges" class="form-control" id="additionalCharges" value="{{ old('additional_charges', $event?->additional_charges ?? 0) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Discount</label>
                        <input type="number" step="0.01" name="discount_amount" class="form-control" id="discountAmount" value="{{ old('discount_amount', $event?->discount_amount ?? 0) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" step="0.01" name="tax_amount" class="form-control" id="taxAmount" value="{{ old('tax_amount', $event?->tax_amount ?? 0) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Advance Paid</label>
                        <input type="number" step="0.01" name="advance_paid" class="form-control" value="{{ old('advance_paid', $event?->advance_paid ?? 0) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control" id="totalDisplay" readonly value="₹{{ number_format($event?->total_amount ?? 0, 2) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="m-section-divider">Notes</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="2">{{ old('special_requests', $event?->special_requests) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Internal Notes</label>
                        <textarea name="internal_notes" class="form-control" rows="2">{{ old('internal_notes', $event?->internal_notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="m-form-actions mb-4">
            <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($event) ? 'Update' : 'Book' }} Event</button>
            <a href="{{ route('admin.banquet.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
var serviceIndex = {{ isset($event) ? $event->services->count() : 0 }};

$('#hallSelect').on('change', function() {
    var opt = $(this).find(':selected');
    if (opt.data('price')) {
        $('#hallCharges').val(opt.data('price'));
    }
});

$('#add-service').on('click', function() {
    var html = '<div class="row service-row mb-2">';
    html += '<div class="col-md-5"><select name="services[' + serviceIndex + '][id]" class="form-select service-select"><option value="">Select Service</option>';
    @foreach($services as $s)
    html += '<option value="{{ $s->id }}" data-price="{{ $s->unit_price }}">{{ $s->name }} (₹{{ $s->unit_price }})</option>';
    @endforeach
    html += '</select></div>';
    html += '<div class="col-md-2"><input type="number" name="services[' + serviceIndex + '][quantity]" class="form-control service-qty" value="1" min="1"></div>';
    html += '<div class="col-md-3"><input type="text" class="form-control service-total" readonly value="₹0.00"></div>';
    html += '<div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm remove-service"><i class="bx bx-trash"></i></button></div>';
    html += '</div>';
    $('#services-container').append(html);
    serviceIndex++;
});

$(document).on('change', '.service-select', function() {
    var row = $(this).closest('.service-row');
    var price = parseFloat($(this).find(':selected').data('price')) || 0;
    var qty = parseInt(row.find('.service-qty').val()) || 1;
    row.find('.service-total').val('₹' + (price * qty).toFixed(2));
});

$(document).on('input', '.service-qty', function() {
    var row = $(this).closest('.service-row');
    var price = parseFloat(row.find('.service-select :selected').data('price')) || 0;
    var qty = parseInt($(this).val()) || 1;
    row.find('.service-total').val('₹' + (price * qty).toFixed(2));
});

$(document).on('click', '.remove-service', function() {
    $(this).closest('.service-row').remove();
});
</script>
@endsection
