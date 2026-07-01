@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="text-end mb-3"><button class="btn btn-sm btn-primary" onclick="window.print()"><i class="bx bx-printer"></i> Print</button></div>
    <div class="card"><div class="card-body">
        <div class="text-center mb-4">
            <h3>{{ config('app.name') }}</h3>
            <h5>Cash Memo / Counter Sale</h5>
        </div>
        <table class="table table-bordered table-sm">
            <tr><td><strong>Sale No:</strong> {{ $spareSale->sale_number }}</td><td><strong>Date:</strong> {{ $spareSale->sale_date->format('d-m-Y') }}</td></tr>
            <tr><td><strong>Customer:</strong> {{ $spareSale->customer->first_name ?? 'Walk-in' }} {{ $spareSale->customer->last_name ?? '' }}</td><td><strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $spareSale->payment_mode)) }}</td></tr>
        </table>

        <table class="table table-bordered table-sm mt-3">
            <thead><tr><th>#</th><th>Part</th><th>HSN</th><th>Qty</th><th>Rate</th><th>GST</th><th>Total</th></tr></thead>
            <tbody>
                @forelse($spareSale->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->part_name }}</td>
                    <td>{{ $item->hsn_code ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                    <td>{{ $item->gst_rate }}%</td>
                    <td class="text-end">{{ number_format($item->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-muted">No items.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr><td colspan="5"></td><td><strong>Subtotal:</strong></td><td class="text-end">{{ number_format($spareSale->subtotal, 2) }}</td></tr>
                <tr><td colspan="5"></td><td><strong>GST:</strong></td><td class="text-end">{{ number_format($spareSale->gst_amount, 2) }}</td></tr>
                <tr><td colspan="5"></td><td><strong>Grand Total:</strong></td><td class="text-end">{{ number_format($spareSale->grand_total, 2) }}</td></tr>
            </tfoot>
        </table>
        <p class="text-center text-muted mt-4">Thank you for your visit!</p>
    </div></div>
</div>
<style media="print">body{background:#fff}.btn{display:none!important}</style>
@endsection
