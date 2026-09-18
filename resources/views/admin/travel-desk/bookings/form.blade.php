@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-car"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($booking) ? 'Edit Transport Booking' : 'New Transport Booking' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Travel Desk</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.travel-desk.bookings.index') }}">Bookings</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($booking) ? 'Edit' : 'New' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.travel-desk.bookings.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($booking) ? route('admin.travel-desk.bookings.update', $booking) : route('admin.travel-desk.bookings.store') }}" method="POST">
                @csrf
                @if(isset($booking)) @method('PUT') @endif

                <div class="m-section-divider">Booking Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $booking?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Transport Type <span class="text-danger">*</span></label>
                        <select name="transport_type_id" class="form-select" id="transportTypeSelect" required>
                            <option value="">Select Type</option>
                            @foreach($types as $type)
                            <option value="{{ $type->id }}" data-base="{{ $type->base_price }}" data-km="{{ $type->per_km_rate }}" data-hour="{{ $type->per_hour_rate }}" {{ old('transport_type_id', $booking?->transport_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }} (₹{{ $type->base_price }} base, {{ $type->max_passengers }} pax)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Trip Type <span class="text-danger">*</span></label>
                        <select name="trip_type" class="form-select" required>
                            <option value="pickup" {{ old('trip_type', $booking?->trip_type ?? 'pickup') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                            <option value="drop" {{ old('trip_type', $booking?->trip_type) == 'drop' ? 'selected' : '' }}>Drop</option>
                            <option value="round_trip" {{ old('trip_type', $booking?->trip_type) == 'round_trip' ? 'selected' : '' }}>Round Trip</option>
                            <option value="hourly" {{ old('trip_type', $booking?->trip_type) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Guest Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guest Name <span class="text-danger">*</span></label>
                        <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name', $booking?->guest_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guest Phone <span class="text-danger">*</span></label>
                        <input type="text" name="guest_phone" class="form-control" value="{{ old('guest_phone', $booking?->guest_phone) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guest Email</label>
                        <input type="email" name="guest_email" class="form-control" value="{{ old('guest_email', $booking?->guest_email) }}">
                    </div>
                </div>

                <div class="m-section-divider">Trip Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pickup Location <span class="text-danger">*</span></label>
                        <input type="text" name="pickup_location" class="form-control" value="{{ old('pickup_location', $booking?->pickup_location) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Drop Location <span class="text-danger">*</span></label>
                        <input type="text" name="drop_location" class="form-control" value="{{ old('drop_location', $booking?->drop_location) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pickup Date/Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="pickup_datetime" class="form-control" value="{{ old('pickup_datetime', $booking?->pickup_datetime?->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Est. Distance (KM)</label>
                        <input type="number" step="0.01" name="estimated_distance_km" class="form-control" id="estDistance" value="{{ old('estimated_distance_km', $booking?->estimated_distance_km) }}" min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Est. Hours</label>
                        <input type="number" step="0.01" name="estimated_hours" class="form-control" id="estHours" value="{{ old('estimated_hours', $booking?->estimated_hours) }}" min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Driver Name</label>
                        <input type="text" name="driver_name" class="form-control" value="{{ old('driver_name', $booking?->driver_name) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Driver Phone</label>
                        <input type="text" name="driver_phone" class="form-control" value="{{ old('driver_phone', $booking?->driver_phone) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Vehicle Number</label>
                        <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number', $booking?->vehicle_number) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Room Charge?</label>
                        <select name="is_room_charge" class="form-select">
                            <option value="no" {{ old('is_room_charge', $booking?->is_room_charge ?? 'no') == 'no' ? 'selected' : '' }}>No</option>
                            <option value="yes" {{ old('is_room_charge', $booking?->is_room_charge) == 'yes' ? 'selected' : '' }}>Yes - Post to Room</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Special Instructions</label>
                        <input type="text" name="special_instructions" class="form-control" value="{{ old('special_instructions', $booking?->special_instructions) }}">
                    </div>
                </div>

                <div class="m-section-divider">Charges</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Additional Charges</label>
                        <input type="number" step="0.01" name="additional_charges" class="form-control" value="{{ old('additional_charges', $booking?->additional_charges ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Discount</label>
                        <input type="number" step="0.01" name="discount_amount" class="form-control" value="{{ old('discount_amount', $booking?->discount_amount ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tax</label>
                        <input type="number" step="0.01" name="tax_amount" class="form-control" value="{{ old('tax_amount', $booking?->tax_amount ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Advance Paid</label>
                        <input type="number" step="0.01" name="advance_paid" class="form-control" value="{{ old('advance_paid', $booking?->advance_paid ?? 0) }}" min="0">
                    </div>
                </div>

                @if(isset($booking))
                <div class="m-section-divider">Status</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ old('status', $booking?->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $booking?->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_progress" {{ old('status', $booking?->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $booking?->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $booking?->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                @endif

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($booking) ? 'Update' : 'Create' }} Booking</button>
                    <a href="{{ route('admin.travel-desk.bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('#transportTypeSelect').on('change', function() {
    var opt = $(this).find(':selected');
    var base = parseFloat(opt.data('base')) || 0;
    var kmRate = parseFloat(opt.data('km')) || 0;
    var hourRate = parseFloat(opt.data('hour')) || 0;
    var dist = parseFloat($('#estDistance').val()) || 0;
    var hrs = parseFloat($('#estHours').val()) || 0;
});

$('#estDistance, #estHours').on('input', function() {
    // auto-calc could be added here
});
</script>
@endsection
