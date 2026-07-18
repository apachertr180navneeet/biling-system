@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Spare Sale #{{ $spareSale->sale_number }}</h4>
        <div>
            <a href="{{ route('admin.spare-sales.print', $spareSale) }}" class="btn btn-sm btn-info" target="_blank"><i class="bx bx-printer"></i> Print</a>
            <a href="{{ route('admin.spare-sales.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    <div class="card"><div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Sale No:</strong> {{ $spareSale->sale_number }}<br>
                <strong>Date:</strong> {{ $spareSale->sale_date->format('d-m-Y') }}<br>
                <strong>Customer:</strong> {{ $spareSale->customer->first_name ?? 'Walk-in' }} {{ $spareSale->customer->last_name ?? '' }}<br>
                <strong>Payment Mode:</strong> {{ ucfirst(str_replace('_', ' ', $spareSale->payment_mode)) }}<br>
                <strong>Tax Mode:</strong> {{ $spareSale->is_gst ? 'GST ('.strtoupper($spareSale->gst_type ?? 'gst').')' : 'Non-GST' }}</p>
            </div>
            <div class="col-md-6 text-end">
                <h3>Total: {{ number_format($spareSale->grand_total, 2) }}</h3>
            </div>
        </div>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Part</th>
                    <th>HSN</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    @if($spareSale->is_gst)
                    <th>GST</th>
                    @endif
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($spareSale->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->part_name }}</td>
                    <td>{{ $item->hsn_code ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->rate, 2) }}</td>
                    @if($spareSale->is_gst)
                    <td>{{ $item->gst_rate }}%</td>
                    @endif
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="{{ $spareSale->is_gst ? 7 : 6 }}" class="text-muted">No items.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr><td colspan="{{ $spareSale->is_gst ? 5 : 4 }}"></td><td><strong>Subtotal:</strong></td><td>{{ number_format($spareSale->subtotal, 2) }}</td></tr>
                @if($spareSale->is_gst)
                <tr><td colspan="5"></td><td><strong>GST:</strong></td><td>{{ number_format($spareSale->gst_amount, 2) }}</td></tr>
                @endif
                <tr><td colspan="{{ $spareSale->is_gst ? 5 : 4 }}"></td><td><strong>Grand Total:</strong></td><td>{{ number_format($spareSale->grand_total, 2) }}</td></tr>
            </tfoot>
        </table>
        @if($spareSale->notes)
        <p><strong>Notes:</strong> {{ $spareSale->notes }}</p>
        @endif
    </div></div>
</div>
@endsection
