@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Goods Receipt Notes /</span> {{ $goodsReceiptNote->grn_number }}
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">GRN Details</h5>
            <a href="{{ route('admin.goods-receipt-notes.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <strong>GRN Number:</strong> {{ $goodsReceiptNote->grn_number }}<br>
                    <strong>Received Date:</strong> {{ $goodsReceiptNote->received_date->format('d-m-Y') }}<br>
                    <strong>Status:</strong>
                    @if($goodsReceiptNote->status == 'completed')
                    <span class="badge bg-success">Completed</span>
                    @else
                    <span class="badge bg-warning">Pending</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <strong>PO Reference:</strong> {{ $goodsReceiptNote->purchaseOrder->order_number ?? '-' }}<br>
                    <strong>Received By:</strong> {{ $goodsReceiptNote->receivedBy->full_name ?? 'System' }}<br>
                    <strong>Notes:</strong> {{ $goodsReceiptNote->notes ?? '-' }}
                </div>
            </div>
            <h5>Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr><th>#</th><th>Part</th><th>Ordered</th><th>Received</th><th>Accepted</th><th>Rejected</th><th>Unit Price</th></tr>
                </thead>
                <tbody>
                    @foreach($goodsReceiptNote->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->sparePart->part_no ?? '-' }} - {{ $item->sparePart->name ?? '-' }}</td>
                        <td>{{ $item->ordered_quantity }}</td>
                        <td>{{ $item->received_quantity }}</td>
                        <td>{{ $item->accepted_quantity }}</td>
                        <td>{{ $item->rejected_quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
