@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Receivable Ageing Report</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer me-1"></i> Print Ageing</button>
    </div>

    <!-- Search Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.receivable-ageing') }}">
                <div class="row g-3">
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="Search by customer name or mobile number..." value="{{ $search }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('admin.reports.receivable-ageing') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th class="text-end">0 - 30 Days</th>
                        <th class="text-end">31 - 60 Days</th>
                        <th class="text-end">61 - 90 Days</th>
                        <th class="text-end">90+ Days</th>
                        <th class="text-end">Total Outstanding (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ageingData as $row)
                        <tr>
                            <td class="fw-bold">{{ $row['customer_name'] }}</td>
                            <td>{{ $row['mobile'] }}</td>
                            <td class="text-end text-success">₹{{ number_format($row['days_0_30'], 2) }}</td>
                            <td class="text-end text-info">₹{{ number_format($row['days_31_60'], 2) }}</td>
                            <td class="text-end text-warning">₹{{ number_format($row['days_61_90'], 2) }}</td>
                            <td class="text-end text-danger fw-bold">₹{{ number_format($row['days_90_plus'], 2) }}</td>
                            <td class="text-end fw-bold text-danger">₹{{ number_format($row['total_due'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No unpaid receivables found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
