@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Stock Summary (Valuation)</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer me-1"></i> Print Summary</button>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Total Vehicles Qty</h6>
                    <h3 class="card-text text-white font-weight-bold mb-0">{{ $totalVehicleQty }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Vehicle Valuation</h6>
                    <h3 class="card-text text-white font-weight-bold mb-0">₹{{ number_format($totalVehicleValuation, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Spare Parts Units</h6>
                    <h3 class="card-text text-white font-weight-bold mb-0">{{ $totalPartQty }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title text-dark">Spare Parts Valuation</h6>
                    <h3 class="card-text text-dark font-weight-bold mb-0">₹{{ number_format($totalPartValuation, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.stock-summary') }}">
                <div class="row g-3">
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="Search item name or part number..." value="{{ $search }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Search</button>
                        <a href="{{ route('admin.reports.stock-summary') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicle Stock Summary Table -->
    <div class="card mb-4">
        <div class="card-header bg-light py-2">
            <h5 class="mb-0">Vehicles Inventory Valuation</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Vehicle Model / Description</th>
                        <th class="text-center">Available Stock Qty</th>
                        <th class="text-end">Ex-Showroom Unit Rate (₹)</th>
                        <th class="text-end">Total Valuation (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicleStock as $v)
                        <tr>
                            <td class="fw-bold">{{ $v['name'] }}</td>
                            <td class="text-center fw-bold text-primary">{{ $v['qty'] }}</td>
                            <td class="text-end">₹{{ number_format($v['avg_rate'], 2) }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($v['total_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">No vehicle inventory available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Parts Stock Summary Table -->
    <div class="card">
        <div class="card-header bg-light py-2">
            <h5 class="mb-0">Spare Parts Inventory Valuation</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Part No</th>
                        <th>Part Name</th>
                        <th class="text-center">Available Qty</th>
                        <th class="text-end">Unit Price (₹)</th>
                        <th class="text-end">Total Valuation (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partStock as $p)
                        <tr>
                            <td><code>{{ $p['part_no'] }}</code></td>
                            <td class="fw-bold">{{ $p['name'] }}</td>
                            <td class="text-center fw-bold text-info">{{ $p['qty'] }}</td>
                            <td class="text-end">₹{{ number_format($p['rate'], 2) }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($p['total_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">No spare parts inventory available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
