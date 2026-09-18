@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-spa"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($appointment) ? 'Edit Appointment' : 'Book Spa Appointment' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Spa</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.spa.appointments.index') }}">Appointments</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($appointment) ? 'Edit' : 'Book' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.spa.appointments.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($appointment) ? route('admin.spa.appointments.update', $appointment) : route('admin.spa.appointments.store') }}" method="POST">
                @csrf
                @if(isset($appointment)) @method('PUT') @endif

                <div class="m-section-divider">Appointment Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $appointment?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Service <span class="text-danger">*</span></label>
                        <select name="spa_service_id" class="form-select" id="spaServiceSelect" required>
                            <option value="">Select Service</option>
                            @foreach($services as $svc)
                            <option value="{{ $svc->id }}" data-price="{{ $svc->price }}" data-duration="{{ $svc->duration_minutes }}" {{ old('spa_service_id', $appointment?->spa_service_id) == $svc->id ? 'selected' : '' }}>{{ $svc->name }} - ₹{{ $svc->price }} ({{ $svc->duration_minutes }} min)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Therapist</label>
                        <input type="text" name="therapist_name" class="form-control" value="{{ old('therapist_name', $appointment?->therapist_name) }}">
                    </div>
                </div>

                <div class="m-section-divider">Guest Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guest Name <span class="text-danger">*</span></label>
                        <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name', $appointment?->guest_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guest Phone <span class="text-danger">*</span></label>
                        <input type="text" name="guest_phone" class="form-control" value="{{ old('guest_phone', $appointment?->guest_phone) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guest Email</label>
                        <input type="email" name="guest_email" class="form-control" value="{{ old('guest_email', $appointment?->guest_email) }}">
                    </div>
                </div>

                <div class="m-section-divider">Schedule</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
                        <input type="date" name="appointment_date" class="form-control" value="{{ old('appointment_date', $appointment?->appointment_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Appointment Time <span class="text-danger">*</span></label>
                        <input type="time" name="appointment_time" class="form-control" value="{{ old('appointment_time', $appointment?->appointment_time) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Charge?</label>
                        <select name="is_room_charge" class="form-select">
                            <option value="no" {{ old('is_room_charge', $appointment?->is_room_charge ?? 'no') == 'no' ? 'selected' : '' }}>No</option>
                            <option value="yes" {{ old('is_room_charge', $appointment?->is_room_charge) == 'yes' ? 'selected' : '' }}>Yes - Post to Room</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Charges</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Base Price</label>
                        <input type="text" id="basePriceDisplay" class="form-control" readonly value="{{ old('price', $appointment?->price ?? 0) }}">
                        <input type="hidden" name="price" id="basePriceHidden" value="{{ old('price', $appointment?->price ?? 0) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Discount</label>
                        <input type="number" step="0.01" name="discount_amount" class="form-control" id="discountAmount" value="{{ old('discount_amount', $appointment?->discount_amount ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tax</label>
                        <input type="number" step="0.01" name="tax_amount" class="form-control" id="taxAmount" value="{{ old('tax_amount', $appointment?->tax_amount ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Advance Paid</label>
                        <input type="number" step="0.01" name="advance_paid" class="form-control" value="{{ old('advance_paid', $appointment?->advance_paid ?? 0) }}" min="0">
                    </div>
                </div>

                <div class="m-section-divider">Notes</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="2">{{ old('special_requests', $appointment?->special_requests) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Internal Notes</label>
                        <textarea name="internal_notes" class="form-control" rows="2">{{ old('internal_notes', $appointment?->internal_notes) }}</textarea>
                    </div>
                </div>

                @if(isset($appointment))
                <div class="m-section-divider">Status</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ old('status', $appointment?->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $appointment?->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_progress" {{ old('status', $appointment?->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $appointment?->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $appointment?->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no_show" {{ old('status', $appointment?->status) == 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                    </div>
                </div>
                @endif

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($appointment) ? 'Update' : 'Book' }} Appointment</button>
                    <a href="{{ route('admin.spa.appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('#spaServiceSelect').on('change', function() {
    var opt = $(this).find(':selected');
    var price = opt.data('price') || 0;
    $('#basePriceDisplay').val(price);
    $('#basePriceHidden').val(price);
});
</script>
@endsection
