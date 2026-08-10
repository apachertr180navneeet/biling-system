@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Party Statement (Ledger)</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer me-1"></i> Print Statement</button>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.party-statement') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Party Type</label>
                        <select name="party_type" class="form-select" onchange="this.form.submit()">
                            <option value="customer" {{ $partyType === 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="supplier" {{ $partyType === 'supplier' ? 'selected' : '' }}>Supplier</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Select {{ ucfirst($partyType) }}</label>
                        <select name="party_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Select {{ ucfirst($partyType) }} --</option>
                            @if($partyType === 'customer')
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ $partyId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            @else
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ $partyId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date Filter</label>
                        <select name="date_filter" class="form-select">
                            <option value="this_month" {{ $dateFilter === 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="last_30_days" {{ $dateFilter === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_year" {{ $dateFilter === 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statement Card -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0">Account Ledger Statement: <span class="text-primary">{{ $partyDetails->name ?? 'Select a Party' }}</span></h5>
            @if($partyDetails)
                <small class="text-muted">Mobile: {{ $partyDetails->mobile ?? '-' }} | Address: {{ $partyDetails->address ?? '-' }}</small>
            @endif
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Particulars / Document</th>
                        <th class="text-end">Billed / Debit (₹)</th>
                        <th class="text-end">Received / Credit (₹)</th>
                        <th class="text-end">Balance Due (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statement as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td class="fw-bold">{{ $row['particulars'] }}</td>
                            <td class="text-end text-danger">₹{{ number_format($row['debit'], 2) }}</td>
                            <td class="text-end text-success">₹{{ number_format($row['credit'], 2) }}</td>
                            <td class="text-end fw-bold text-dark">₹{{ number_format($row['balance'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No transactions recorded for the selected party.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
