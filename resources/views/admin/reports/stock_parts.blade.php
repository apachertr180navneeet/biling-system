@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Spare Part Stock Report</h4>
    <div class="row mb-4">
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h5>{{ $totalParts }}</h5><small class="text-muted">Total Quantity</small></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h5 class="text-warning">{{ $lowStock }}</h5><small class="text-muted">Low Stock Items</small></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h5 class="text-danger">{{ $outOfStock }}</h5><small class="text-muted">Out of Stock</small></div></div></div>
    </div>
    <div class="card"><div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Part</th><th>Category</th><th>Location</th><th>Quantity</th><th>Min Qty</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($stocks as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->sparePart->part_name ?? '-' }}</td>
                        <td>{{ $s->sparePart->category->name ?? '-' }}</td>
                        <td>{{ $s->location ?? '-' }}</td>
                        <td>{{ $s->quantity }}</td>
                        <td>{{ $s->min_quantity }}</td>
                        <td>
                            @if($s->quantity <= 0)<span class="badge bg-danger">Out of Stock</span>
                            @elseif($s->quantity <= $s->min_quantity)<span class="badge bg-warning">Low Stock</span>
                            @else<span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">No data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $stocks->links() }}</div>
    </div></div>
</div>
@endsection
