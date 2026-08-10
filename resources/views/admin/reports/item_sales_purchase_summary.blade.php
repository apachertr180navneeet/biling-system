@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Item Sales and Purchase Summary</h4>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.item-sales-purchase-summary') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Search Item</label>
                        <input type="text" name="search" class="form-control" placeholder="Search by item / model / part no..." value="{{ $search }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date Filter</label>
                        <select name="date_filter" class="form-select">
                            <option value="this_month" {{ $dateFilter === 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="last_30_days" {{ $dateFilter === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_year" {{ $dateFilter === 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-filter-alt me-1"></i> Apply Filter</button>
                        <a href="{{ route('admin.reports.item-sales-purchase-summary') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Item-wise Summary</h5>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer"></i> Print</button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Type</th>
                        <th class="text-center">Pur. Qty</th>
                        <th class="text-end">Pur. Amount (₹)</th>
                        <th class="text-center">Sales Qty</th>
                        <th class="text-end">Sales Amount (₹)</th>
                        <th class="text-end">Net Margin (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $row)
                        <tr>
                            <td class="fw-bold">{{ $row['name'] }}</td>
                            <td><span class="badge bg-label-secondary">{{ $row['type'] }}</span></td>
                            <td class="text-center">{{ $row['purchase_qty'] }}</td>
                            <td class="text-end">₹{{ number_format($row['purchase_amount'], 2) }}</td>
                            <td class="text-center fw-bold text-primary">{{ $row['sales_qty'] }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($row['sales_amount'], 2) }}</td>
                            <td class="text-end fw-bold {{ $row['net_margin'] >= 0 ? 'text-success' : 'text-danger' }}">
                                ₹{{ number_format($row['net_margin'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No item transactions found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
