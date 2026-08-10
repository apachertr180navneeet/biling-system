@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Item Report By Party</h4>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.item-report-by-party') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Party Type</label>
                        <select name="party_type" id="party_type_select" class="form-select" onchange="this.form.submit()">
                            <option value="customer" {{ $partyType === 'customer' ? 'selected' : '' }}>Customer (Sales)</option>
                            <option value="supplier" {{ $partyType === 'supplier' ? 'selected' : '' }}>Supplier (Purchases)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Select {{ ucfirst($partyType) }}</label>
                        <select name="party_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Select {{ ucfirst($partyType) }} --</option>
                            @if($partyType === 'customer')
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ $partyId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->mobile ?? 'No Mobile' }})</option>
                                @endforeach
                            @else
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ $partyId == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->mobile ?? 'No Mobile' }})</option>
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

    <!-- Data Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Item Summary for: <span class="text-primary">{{ $selectedPartyName ?: 'No Party Selected' }}</span>
            </h5>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer"></i> Print</button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Item Description</th>
                        <th>Type</th>
                        <th class="text-center">Total Quantity</th>
                        <th class="text-end">Total Amount (₹)</th>
                        <th class="text-center">Last Transaction Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $item['item_name'] }}</td>
                            <td><span class="badge bg-label-info">{{ $item['type'] }}</span></td>
                            <td class="text-center fw-bold">{{ $item['total_qty'] }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($item['total_amount'], 2) }}</td>
                            <td class="text-center">{{ $item['last_date'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No items found for the selected party.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
