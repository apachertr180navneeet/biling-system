@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Vehicle Stock Report</h4>
    <div class="row mb-4">
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h5>{{ $total }}</h5><small class="text-muted">Total Vehicles</small></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h5 class="text-success">{{ $available }}</h5><small class="text-muted">Available</small></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body text-center"><h5 class="text-secondary">{{ $sold }}</h5><small class="text-muted">Sold</small></div></div></div>
    </div>
    <div class="card"><div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Brand</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse($byBrand as $b)
                    <tr><td>{{ $loop->iteration }}</td><td>{{ $b->brand }}</td><td>{{ $b->total }}</td></tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">No data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>
</div>
@endsection
