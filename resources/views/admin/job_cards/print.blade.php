@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="text-end mb-3"><button class="btn btn-sm btn-primary" onclick="window.print()"><i class="bx bx-printer"></i> Print</button></div>
    <div class="card"><div class="card-body">
        <div class="text-center mb-4">
            <h3>{{ config('app.name') }}</h3>
            <h5>Job Card / Service Invoice</h5>
        </div>
        <table class="table table-bordered">
            <tr><td><strong>Job Card No:</strong> {{ $jobCard->job_card_number }}</td><td><strong>Date:</strong> {{ $jobCard->service_date->format('d-m-Y') }}</td></tr>
            <tr><td><strong>Customer:</strong> {{ $jobCard->customer->first_name ?? '' }} {{ $jobCard->customer->last_name ?? '' }}</td><td><strong>Status:</strong> {{ ucfirst($jobCard->status) }}</td></tr>
            <tr><td><strong>Vehicle:</strong> {{ $jobCard->vehicle_number ?? '-' }}</td><td><strong>Model:</strong> {{ $jobCard->vehicle_model ?? '-' }}</td></tr>
            <tr><td colspan="2"><strong>Complaint:</strong> {{ $jobCard->complaint ?? '-' }}</td></tr>
        </table>

        <h5 class="mt-4">Services Performed</h5>
        <table class="table table-sm table-bordered">
            <thead><tr><th>#</th><th>Service</th><th>Amount</th></tr></thead>
            <tbody>
                @forelse($jobCard->services as $svc)
                <tr><td>{{ $loop->iteration }}</td><td>{{ $svc->service_name }}</td><td class="text-end">{{ number_format($svc->labor_charge, 2) }}</td></tr>
                @empty
                <tr><td colspan="3" class="text-muted">No services.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h5>Parts Used</h5>
        <table class="table table-sm table-bordered">
            <thead><tr><th>#</th><th>Part</th><th>Qty</th><th>Rate</th><th>GST</th><th>Total</th></tr></thead>
            <tbody>
                @forelse($jobCard->parts as $part)
                <tr><td>{{ $loop->iteration }}</td><td>{{ $part->part_name }}</td><td>{{ $part->quantity }}</td><td class="text-end">{{ number_format($part->rate, 2) }}</td><td>{{ $part->gst_rate }}%</td><td class="text-end">{{ number_format($part->total, 2) }}</td></tr>
                @empty
                <tr><td colspan="6" class="text-muted">No parts.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="row mt-3">
            <div class="col-md-6 offset-md-6">
                <table class="table table-sm">
                    <tr><td><strong>Total Labor:</strong></td><td class="text-end">{{ number_format($jobCard->total_labor, 2) }}</td></tr>
                    <tr><td><strong>Total Parts:</strong></td><td class="text-end">{{ number_format($jobCard->total_parts, 2) }}</td></tr>
                    @if($jobCard->is_gst)
                    <tr><td><strong>GST ({{ strtoupper($jobCard->gst_type ?? '-') }}):</strong></td><td class="text-end">{{ number_format($jobCard->gst_amount, 2) }}</td></tr>
                    @endif
                    <tr class="fw-bold"><td><strong>Grand Total:</strong></td><td class="text-end">{{ number_format($jobCard->grand_total, 2) }}</td></tr>
                </table>
            </div>
        </div>
        <p class="text-center text-muted mt-4">Thank you for your business!</p>
    </div></div>
</div>
<style media="print">body{background:#fff}.btn{display:none!important}</style>
@endsection
