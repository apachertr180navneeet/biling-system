@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Job Card #{{ $jobCard->job_card_number }}</h4>
        <div>
            <a href="{{ route('admin.job-cards.edit', $jobCard) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.job-cards.print', $jobCard) }}" class="btn btn-sm btn-info" target="_blank"><i class="bx bx-printer"></i> Print</a>
            <a href="{{ route('admin.job-cards.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card"><div class="card-body">
                <h5>Customer Details</h5>
                <p><strong>Name:</strong> {{ $jobCard->customer->first_name ?? '' }} {{ $jobCard->customer->last_name ?? '' }}<br>
                <strong>Mobile:</strong> {{ $jobCard->customer->mobile ?? '-' }}<br>
                <strong>Vehicle:</strong> {{ $jobCard->vehicle_number ?? $jobCard->vehicle_model ?? '-' }}<br>
                <strong>KM Reading:</strong> {{ $jobCard->kilometer_reading ?? '-' }}</p>
            </div></div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card"><div class="card-body">
                <h5>Service Details</h5>
                <p><strong>Status:</strong> <span class="badge bg-{{ $jobCard->status == 'pending' ? 'secondary' : ($jobCard->status == 'in_progress' ? 'info' : ($jobCard->status == 'completed' ? 'success' : 'primary')) }}">{{ ucfirst(str_replace('_', ' ', $jobCard->status)) }}</span><br>
                <strong>Service Date:</strong> {{ $jobCard->service_date->format('d-m-Y') }}<br>
                <strong>Completion Date:</strong> {{ $jobCard->completion_date ? $jobCard->completion_date->format('d-m-Y') : '-' }}<br>
                <strong>Complaint:</strong> {{ $jobCard->complaint ?? '-' }}</p>
            </div></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card"><div class="card-body">
                <h5>Services Performed</h5>
                <table class="table table-sm">
                    <thead><tr><th>#</th><th>Service</th><th>Labor Charge</th></tr></thead>
                    <tbody>
                        @forelse($jobCard->services as $svc)
                        <tr><td>{{ $loop->iteration }}</td><td>{{ $svc->service_name }}</td><td>{{ number_format($svc->labor_charge, 2) }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="text-muted">No services added.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <h5 class="mt-3">Parts Used</h5>
                <table class="table table-sm">
                    <thead><tr><th>#</th><th>Part</th><th>Qty</th><th>Rate</th><th>GST</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($jobCard->parts as $part)
                        <tr><td>{{ $loop->iteration }}</td><td>{{ $part->part_name }}</td><td>{{ $part->quantity }}</td><td>{{ number_format($part->rate, 2) }}</td><td>{{ $part->gst_rate }}%</td><td>{{ number_format($part->total, 2) }}</td></tr>
                        @empty
                        <tr><td colspan="6" class="text-muted">No parts used.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <hr>
                <div class="text-end">
                    <p><strong>Total Labor:</strong> {{ number_format($jobCard->total_labor, 2) }}</p>
                    <p><strong>Total Parts:</strong> {{ number_format($jobCard->total_parts, 2) }}</p>
                    @if($jobCard->is_gst)
                    <p><strong>GST ({{ strtoupper($jobCard->gst_type ?? '-') }}):</strong> {{ number_format($jobCard->gst_amount, 2) }}</p>
                    @endif
                    <h4><strong>Grand Total: {{ number_format($jobCard->grand_total, 2) }}</strong></h4>
                </div>
            </div></div>
        </div>
    </div>
</div>
@endsection
