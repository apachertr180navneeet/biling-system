@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Rate List / Price Catalog</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer me-1"></i> Print Rate List</button>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.rate-list') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, part no, model..." value="{{ $search }}">
                    </div>
                    <div class="col-md-4">
                        <select name="type" class="form-select">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Items (Vehicles & Parts)</option>
                            <option value="vehicle" {{ $type === 'vehicle' ? 'selected' : '' }}>Vehicles Only</option>
                            <option value="part" {{ $type === 'part' ? 'selected' : '' }}>Spare Parts Only</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('admin.reports.rate-list') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Item Code / SKU</th>
                        <th>Item Name / Description</th>
                        <th>Category / Type</th>
                        <th class="text-end">Purchase Price (₹)</th>
                        <th class="text-end">Selling Price / MRP (₹)</th>
                        <th class="text-center">GST Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rates as $index => $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><code>{{ $row['code_no'] }}</code></td>
                            <td class="fw-bold">{{ $row['name'] }}</td>
                            <td><span class="badge bg-label-info">{{ $row['type'] }}</span></td>
                            <td class="text-end">₹{{ number_format($row['purchase_price'], 2) }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($row['sale_price'], 2) }}</td>
                            <td class="text-center"><span class="badge bg-label-dark">{{ $row['gst_rate'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No items matching rate list criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
