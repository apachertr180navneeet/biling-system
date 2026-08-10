@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0 text-danger"><i class="bx bx-error-alt me-1"></i> Low Stock Summary</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer me-1"></i> Print Summary</button>
    </div>

    <!-- Search Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.low-stock-summary') }}">
                <div class="row g-3">
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="Search low stock items..." value="{{ $search }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('admin.reports.low-stock-summary') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicles Low Stock Table -->
    <div class="card mb-4 border-start border-danger border-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light py-2">
            <h5 class="mb-0 text-dark font-weight-bold">Low Stock Vehicles</h5>
            <span class="badge bg-danger">{{ count($lowVehiclesList) }} Items</span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Vehicle Variant / Model</th>
                        <th class="text-center">Current Available</th>
                        <th class="text-center">Minimum Threshold</th>
                        <th class="text-center">Deficit Qty</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowVehiclesList as $v)
                        <tr>
                            <td class="fw-bold">{{ $v['variant_name'] }}</td>
                            <td class="text-center fw-bold text-danger">{{ $v['current_stock'] }}</td>
                            <td class="text-center">{{ $v['min_stock'] }}</td>
                            <td class="text-center text-warning fw-bold">+{{ $v['deficit'] }}</td>
                            <td>
                                <a href="{{ route('admin.vehicle-purchase-orders.create') }}" class="btn btn-xs btn-primary"><i class="bx bx-plus"></i> Create Purchase Order</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">All vehicle stocks are currently healthy!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Spare Parts Low Stock Table -->
    <div class="card border-start border-warning border-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light py-2">
            <h5 class="mb-0 text-dark font-weight-bold">Low Stock Spare Parts</h5>
            <span class="badge bg-warning text-dark">{{ count($lowPartsList) }} Items</span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Part No</th>
                        <th>Part Name</th>
                        <th class="text-center">Current Stock</th>
                        <th class="text-center">Minimum Stock</th>
                        <th class="text-end">Unit Price (₹)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowPartsList as $p)
                        <tr>
                            <td><code>{{ $p->part_no ?? '-' }}</code></td>
                            <td class="fw-bold">{{ $p->name }}</td>
                            <td class="text-center fw-bold text-danger">{{ $p->current_stock }}</td>
                            <td class="text-center">{{ $p->min_stock }}</td>
                            <td class="text-end">₹{{ number_format($p->price, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-xs btn-warning"><i class="bx bx-cart"></i> Order Stock</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">All spare part stocks are currently healthy!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
