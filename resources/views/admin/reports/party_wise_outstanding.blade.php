@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Party Wise Outstanding Report</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer me-1"></i> Print Report</button>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.party-wise-outstanding') }}">
                <div class="row g-3">
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="Search by party name or phone..." value="{{ $search }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('admin.reports.party-wise-outstanding') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Party Name</th>
                        <th>Party Category</th>
                        <th>Contact Mobile</th>
                        <th class="text-end">Outstanding Balance (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partyList as $index => $party)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $party['name'] }}</td>
                            <td>
                                @if(str_contains($party['type'], 'Receivable'))
                                    <span class="badge bg-label-danger">{{ $party['type'] }}</span>
                                @else
                                    <span class="badge bg-label-warning">{{ $party['type'] }}</span>
                                @endif
                            </td>
                            <td>{{ $party['phone'] }}</td>
                            <td class="text-end fw-bold text-danger">₹{{ number_format($party['total_outstanding'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No outstanding balances found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
