@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Purchase Parts Stock Report</h4>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-bold">Total Items:</span> {{ $stocks->count() }} &nbsp;|&nbsp;
                <span class="fw-bold">Total Stock Value:</span> ₹{{ number_format($totalValue, 2) }}
            </div>
        </div>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Part No</th>
                        <th>Part Name</th>
                        <th>Category</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Qty</th>
                        <th>Stock Value</th>
                        <th>PO No</th>
                        <th>PO Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $s->sparePart->part_no ?? '-' }}</td>
                        <td>{{ $s->sparePart->name ?? '-' }}</td>
                        <td>{{ $s->sparePart->category ?? '-' }}</td>
                        <td>₹{{ number_format($s->purchase_price, 2) }}</td>
                        <td>₹{{ number_format($s->sparePart->selling_price ?? 0, 2) }}</td>
                        <td><strong>{{ $s->quantity }}</strong></td>
                        <td>₹{{ number_format($s->quantity * $s->purchase_price, 2) }}</td>
                        <td>{{ $s->purchaseOrder->order_number ?? '-' }}</td>
                        <td>{{ $s->purchaseOrder->order_date ? $s->purchaseOrder->order_date->format('d-m-Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted">No stock records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
