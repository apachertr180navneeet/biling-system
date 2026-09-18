@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-calendar-event"></i></div>
            <div>
                <h4 class="m-page-title">Event: {{ $event->event_name }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Banquet</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.banquet.events.index') }}">Events</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">View</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasPermission('events.edit'))
            <a href="{{ route('admin.banquet.events.edit', $event) }}" class="btn btn-primary"><i class="bx bx-edit me-1"></i> Edit</a>
            @endif
            <a href="{{ route('admin.banquet.events.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Event Details</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr><th width="45%">Event Number</th><td>{{ $event->event_number }}</td></tr>
                                <tr><th>Event Name</th><td>{{ $event->event_name }}</td></tr>
                                <tr><th>Event Type</th><td><span class="badge bg-label-primary">{{ ucfirst($event->event_type) }}</span></td></tr>
                                <tr><th>Hall</th><td>{{ $event->hall->name ?? '-' }}</td></tr>
                                <tr><th>Hotel</th><td>{{ $event->hotel->name ?? '-' }}</td></tr>
                                <tr><th>Event Date</th><td>{{ $event->event_date->format('d-m-Y') }}</td></tr>
                                <tr><th>Time</th><td>{{ $event->start_time }} - {{ $event->end_time }}</td></tr>
                                <tr><th>Expected Guests</th><td>{{ $event->expected_guests }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr><th width="45%">Contact Name</th><td>{{ $event->contact_name }}</td></tr>
                                <tr><th>Contact Phone</th><td>{{ $event->contact_phone }}</td></tr>
                                <tr><th>Contact Email</th><td>{{ $event->contact_email ?? '-' }}</td></tr>
                                <tr><th>Booking Status</th>
                                    <td>
                                        @php $colors = ['inquiry'=>'secondary','proposed'=>'info','confirmed'=>'primary','in_progress'=>'warning','completed'=>'success','cancelled'=>'danger']; @endphp
                                        <span class="badge bg-label-{{ $colors[$event->booking_status] ?? 'secondary' }}">{{ str_replace('_', ' ', ucfirst($event->booking_status)) }}</span>
                                    </td>
                                </tr>
                                <tr><th>Payment Status</th>
                                    <td>
                                        @php $pColors = ['unpaid'=>'danger','partial'=>'warning','paid'=>'success','refunded'=>'info']; @endphp
                                        <span class="badge bg-label-{{ $pColors[$event->payment_status] ?? 'secondary' }}">{{ ucfirst($event->payment_status) }}</span>
                                    </td>
                                </tr>
                                <tr><th>Created By</th><td>{{ $event->createdBy->name ?? '-' }}</td></tr>
                                <tr><th>Approved By</th><td>{{ $event->approvedBy->name ?? '-' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Charges</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th>Hall Charges</th><td class="text-end">₹{{ number_format($event->hall_charges, 2) }}</td></tr>
                        <tr><th>Services</th><td class="text-end">₹{{ number_format($event->services_charges, 2) }}</td></tr>
                        <tr><th>Additional</th><td class="text-end">₹{{ number_format($event->additional_charges, 2) }}</td></tr>
                        @if($event->discount_amount > 0)
                        <tr><th>Discount</th><td class="text-end text-danger">-₹{{ number_format($event->discount_amount, 2) }}</td></tr>
                        @endif
                        @if($event->tax_amount > 0)
                        <tr><th>Tax</th><td class="text-end">₹{{ number_format($event->tax_amount, 2) }}</td></tr>
                        @endif
                        <tr class="border-top"><th>Total</th><td class="text-end fw-bold">₹{{ number_format($event->total_amount, 2) }}</td></tr>
                        <tr><th>Advance Paid</th><td class="text-end text-success">₹{{ number_format($event->advance_paid, 2) }}</td></tr>
                        <tr><th>Balance</th><td class="text-end fw-bold text-danger">₹{{ number_format($event->balance_amount, 2) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($event->services->count() > 0)
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Services</h5></div>
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <thead><tr><th>Service</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Notes</th></tr></thead>
                <tbody>
                    @foreach($event->services as $svc)
                    <tr>
                        <td>{{ $svc->name }}</td>
                        <td>{{ $svc->pivot->quantity }}</td>
                        <td>₹{{ number_format($svc->pivot->unit_price, 2) }}</td>
                        <td>₹{{ number_format($svc->pivot->total_price, 2) }}</td>
                        <td>{{ $svc->pivot->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($event->special_requests || $event->internal_notes)
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Notes</h5></div>
        <div class="card-body">
            @if($event->special_requests)
            <p><strong>Special Requests:</strong> {{ $event->special_requests }}</p>
            @endif
            @if($event->internal_notes)
            <p><strong>Internal Notes:</strong> {{ $event->internal_notes }}</p>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
