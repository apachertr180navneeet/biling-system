@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-car"></i></div>
            <div>
                <h4 class="m-page-title">Booking #{{ $booking->booking_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Travel Desk</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.travel-desk.bookings.index') }}">Bookings</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $booking->booking_number }}</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.travel-desk.bookings.toll-print', $booking) }}" class="btn btn-outline-primary" target="_blank"><i class="bx bx-printer me-1"></i> Toll Print</a>
            @if(auth()->user()->hasPermission('transport_bookings.edit'))
            <a href="{{ route('admin.travel-desk.bookings.edit', $booking) }}" class="btn btn-primary"><i class="bx bx-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('admin.travel-desk.bookings.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Booking #</strong><br>{{ $booking->booking_number }}</div>
                        <div class="col-md-3"><strong>Transport Type</strong><br>{{ $booking->transportType->name ?? '' }}</div>
                        <div class="col-md-3"><strong>Trip Type</strong><br>{{ ucfirst(str_replace('_', ' ', $booking->trip_type)) }}</div>
                        <div class="col-md-3"><strong>Status</strong><br>
                            @php
                                $colors = ['pending' => 'warning', 'confirmed' => 'primary', 'in_progress' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                            @endphp
                            <span class="badge bg-label-{{ $colors[$booking->status] ?? 'secondary' }}">{{ str_replace('_', ' ', ucfirst($booking->status)) }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Pickup Location</strong><br>{{ $booking->pickup_location }}</div>
                        <div class="col-md-6"><strong>Drop Location</strong><br>{{ $booking->drop_location }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Pickup Date/Time</strong><br>{{ $booking->pickup_datetime?->format('d-m-Y H:i') }}</div>
                        <div class="col-md-4"><strong>Est. Distance</strong><br>{{ $booking->estimated_distance_km ? $booking->estimated_distance_km . ' KM' : 'N/A' }}</div>
                        <div class="col-md-4"><strong>Est. Hours</strong><br>{{ $booking->estimated_hours ? $booking->estimated_hours . ' hrs' : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Guest Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Guest Name</strong><br>{{ $booking->guest_name }}</div>
                        <div class="col-md-4"><strong>Phone</strong><br>{{ $booking->guest_phone }}</div>
                        <div class="col-md-4"><strong>Email</strong><br>{{ $booking->guest_email ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Driver Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Driver Name</strong><br>{{ $booking->driver_name ?? 'Not assigned' }}</div>
                        <div class="col-md-4"><strong>Driver Phone</strong><br>{{ $booking->driver_phone ?? 'N/A' }}</div>
                        <div class="col-md-4"><strong>Vehicle Number</strong><br>{{ $booking->vehicle_number ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Charges</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Base Price</span><span>₹{{ number_format($booking->base_price, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Distance Charges</span><span>₹{{ number_format($booking->distance_charges, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Hourly Charges</span><span>₹{{ number_format($booking->hourly_charges, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Additional</span><span>₹{{ number_format($booking->additional_charges, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Discount</span><span>- ₹{{ number_format($booking->discount_amount, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Tax</span><span>₹{{ number_format($booking->tax_amount, 2) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2 fw-bold"><span>Total</span><span>₹{{ number_format($booking->total_amount, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Advance Paid</span><span>₹{{ number_format($booking->advance_paid, 2) }}</span></div>
                    <div class="d-flex justify-content-between fw-bold text-primary"><span>Balance</span><span>₹{{ number_format($booking->total_amount - $booking->advance_paid, 2) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Payment Status</span>
                        @php $pColors = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success']; @endphp
                        <span class="badge bg-label-{{ $pColors[$booking->payment_status] ?? 'secondary' }}">{{ ucfirst($booking->payment_status) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2"><span>Room Charge</span><span>{{ $booking->is_room_charge == 'yes' ? 'Yes' : 'No' }}</span></div>
                </div>
            </div>

            @if($booking->special_instructions)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Special Instructions</h5>
                </div>
                <div class="card-body">{{ $booking->special_instructions }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
