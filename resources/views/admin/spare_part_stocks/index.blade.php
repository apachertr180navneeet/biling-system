@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Spare Part Inventory
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Stock Levels</h5></div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Part No.</th>
                        <th>Part Name</th>
                        <th>Category</th>
                        <th>Qty</th>
                        <th>Purchase Price</th>
                        <th>Status</th>
                        <th>PO Ref</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $s)
                    <tr class="@if($s->min_quantity > 0 && $s->quantity < $s->min_quantity) table-danger @endif">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->sparePart->part_no ?? '-' }}</td>
                        <td>{{ $s->sparePart->name ?? '-' }}</td>
                        <td>{{ $s->sparePart->category->name ?? '-' }}</td>
                        <td><strong>{{ $s->quantity }}</strong></td>
                        <td>{{ number_format($s->purchase_price, 2) }}</td>
                        <td>
                            @if($s->min_quantity > 0 && $s->quantity < $s->min_quantity)
                            <span class="badge bg-danger">Low Stock</span>
                            @elseif($s->quantity < 1)
                            <span class="badge bg-secondary">Out of Stock</span>
                            @else
                            <span class="badge bg-success">Available</span>
                            @endif
                        </td>
                        <td>
                            @if($s->purchaseOrder)
                            <a href="{{ route('admin.purchase-orders.show', $s->purchaseOrder) }}">{{ $s->purchaseOrder->order_number }}</a>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No stock records. Receive parts via Purchase Orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $stocks->links() }}</div>
    </div>
</div>
@endsection
