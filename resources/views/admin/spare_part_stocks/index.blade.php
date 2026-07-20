@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Spare Part Inventory
    </h4>

    <!-- Search filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.spare-part-stocks.index') }}">
                <div class="row g-3">
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="Search by Part No or Part Name" value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Stock Levels</h5>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#adjustStockModal">Adjust Stock</button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Part No.</th>
                        <th>Part Name</th>
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

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.spare-part-stocks.adjust') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Part Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="spare_part_id" class="form-label">Select Part</label>
                        <select name="spare_part_id" id="spare_part_id" class="form-select" required>
                            <option value="">-- Select Spare Part --</option>
                            @foreach($spareParts as $p)
                            <option value="{{ $p->id }}">{{ $p->part_no }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjustment_type" class="form-label">Adjustment Type</label>
                        <select name="adjustment_type" id="adjustment_type" class="form-select" required>
                            <option value="in">Stock In (+)</option>
                            <option value="out">Stock Out (-)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes / Reference</label>
                        <input type="text" name="notes" id="notes" class="form-control" placeholder="e.g. Sold to customer, Manual count adjustment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
