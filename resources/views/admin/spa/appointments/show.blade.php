@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-spa"></i></div>
            <div>
                <h4 class="m-page-title">Appointment #{{ $appointment->appointment_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Spa</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.spa.appointments.index') }}">Appointments</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $appointment->appointment_number }}</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasPermission('spa_appointments.edit'))
            <a href="{{ route('admin.spa.appointments.edit', $appointment) }}" class="btn btn-primary"><i class="bx bx-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('admin.spa.appointments.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Appointment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Appointment #</strong><br>{{ $appointment->appointment_number }}</div>
                        <div class="col-md-3"><strong>Service</strong><br>{{ $appointment->spaService->name ?? '' }}</div>
                        <div class="col-md-3"><strong>Date</strong><br>{{ $appointment->appointment_date?->format('d-m-Y') }}</div>
                        <div class="col-md-3"><strong>Time</strong><br>{{ $appointment->appointment_time }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Duration</strong><br>{{ $appointment->duration_minutes }} min</div>
                        <div class="col-md-3"><strong>Therapist</strong><br>{{ $appointment->therapist_name ?? 'N/A' }}</div>
                        <div class="col-md-3"><strong>Hotel</strong><br>{{ $appointment->hotel->name ?? '' }}</div>
                        <div class="col-md-3"><strong>Status</strong><br>
                            @php
                                $colors = ['pending' => 'warning', 'confirmed' => 'primary', 'in_progress' => 'info', 'completed' => 'success', 'cancelled' => 'danger', 'no_show' => 'secondary'];
                            @endphp
                            <span class="badge bg-label-{{ $colors[$appointment->status] ?? 'secondary' }}">{{ str_replace('_', ' ', ucfirst($appointment->status)) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Guest Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Guest Name</strong><br>{{ $appointment->guest_name }}</div>
                        <div class="col-md-4"><strong>Phone</strong><br>{{ $appointment->guest_phone }}</div>
                        <div class="col-md-4"><strong>Email</strong><br>{{ $appointment->guest_email ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            @if($appointment->special_requests)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Special Requests</h5>
                </div>
                <div class="card-body">{{ $appointment->special_requests }}</div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Charges</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Base Price</span><span>₹{{ number_format($appointment->price, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Discount</span><span>- ₹{{ number_format($appointment->discount_amount, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Tax</span><span>₹{{ number_format($appointment->tax_amount, 2) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2 fw-bold"><span>Total</span><span>₹{{ number_format($appointment->total_amount, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Advance Paid</span><span>₹{{ number_format($appointment->advance_paid, 2) }}</span></div>
                    <div class="d-flex justify-content-between fw-bold text-primary"><span>Balance</span><span>₹{{ number_format($appointment->total_amount - $appointment->advance_paid, 2) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Payment Status</span>
                        @php $pColors = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success']; @endphp
                        <span class="badge bg-label-{{ $pColors[$appointment->payment_status] ?? 'secondary' }}">{{ ucfirst($appointment->payment_status) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2"><span>Room Charge</span><span>{{ $appointment->is_room_charge == 'yes' ? 'Yes' : 'No' }}</span></div>
                </div>
            </div>

            @if($appointment->internal_notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Internal Notes</h5>
                </div>
                <div class="card-body">{{ $appointment->internal_notes }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
