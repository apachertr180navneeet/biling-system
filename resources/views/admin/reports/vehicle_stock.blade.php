@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Vehicle Stock Report</h4>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-bold">Total Vehicles:</span> {{ $totalQty }} &nbsp;|&nbsp;
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
                        <th>Vehicle Description</th>
                        <th>Chassis No</th>
                        <th>Engine No</th>
                        <th>Color</th>
                        <th>Mfg Year</th>
                        <th>Qty</th>
                        <th>Purchase Price</th>
                        <th>Stock Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $i => $v)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $v->vehicle_description ?? '-' }}</td>
                        <td>{{ $v->chassis_number ?? '-' }}</td>
                        <td>{{ $v->engine_number ?? '-' }}</td>
                        <td>{{ $v->color_name ?? '-' }}</td>
                        <td>{{ $v->mfg_year ?? '-' }}</td>
                        <td><strong>{{ $v->quantity }}</strong></td>
                        <td>₹{{ number_format($v->purchase_price, 2) }}</td>
                        <td>₹{{ number_format($v->quantity * $v->purchase_price, 2) }}</td>
                        <td>
                            @if($v->status == 'available')
                                <span class="badge bg-success">Available</span>
                            @elseif($v->status == 'sold')
                                <span class="badge bg-danger">Sold</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($v->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted">No vehicle records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
