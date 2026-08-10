@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Sales Summary - Category Wise</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer me-1"></i> Print Summary</button>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.sales-summary-category') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-9">
                        <label class="form-label fw-bold">Date Filter</label>
                        <select name="date_filter" class="form-select">
                            <option value="this_month" {{ $dateFilter === 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="last_30_days" {{ $dateFilter === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_year" {{ $dateFilter === 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('admin.reports.sales-summary-category') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Category Name</th>
                        <th class="text-center">Total Units Sold</th>
                        <th class="text-end">Taxable Sales (₹)</th>
                        <th class="text-end">Tax Collected (₹)</th>
                        <th class="text-end">Grand Total Revenue (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold"><i class="bx bx-car text-primary me-1"></i> Vehicles (Sales Invoices)</td>
                        <td class="text-center fw-bold">{{ $vehicleSales->total_units ?? 0 }}</td>
                        <td class="text-end">₹{{ number_format($vehicleSales->total_taxable ?? 0, 2) }}</td>
                        <td class="text-end text-info">₹{{ number_format($vehicleSales->total_tax ?? 0, 2) }}</td>
                        <td class="text-end fw-bold text-success">₹{{ number_format($vehicleSales->grand_revenue ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bx bx-wrench text-warning me-1"></i> Spare Parts & Accessories</td>
                        <td class="text-center fw-bold">{{ $partSales->total_units ?? 0 }}</td>
                        <td class="text-end">₹{{ number_format($partSales->total_taxable ?? 0, 2) }}</td>
                        <td class="text-end text-info">₹{{ number_format($partSales->total_tax ?? 0, 2) }}</td>
                        <td class="text-end fw-bold text-success">₹{{ number_format($partSales->grand_revenue ?? 0, 2) }}</td>
                    </tr>
                    <tr class="table-light fw-bold">
                        <td>TOTAL COMBINED SALES</td>
                        <td class="text-center">{{ ($vehicleSales->total_units ?? 0) + ($partSales->total_units ?? 0) }}</td>
                        <td class="text-end">₹{{ number_format(($vehicleSales->total_taxable ?? 0) + ($partSales->total_taxable ?? 0), 2) }}</td>
                        <td class="text-end text-info">₹{{ number_format(($vehicleSales->total_tax ?? 0) + ($partSales->total_tax ?? 0), 2) }}</td>
                        <td class="text-end text-success">₹{{ number_format(($vehicleSales->grand_revenue ?? 0) + ($partSales->grand_revenue ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
