@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Stock Detail Report</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bx bx-printer me-1"></i> Print Log</button>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.stock-detail-report') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Select Item</label>
                        <select name="item_id" class="form-select" onchange="this.form.submit()">
                            @foreach($itemList as $item)
                                <option value="{{ $item['id'] }}" {{ $selectedItem === $item['id'] ? 'selected' : '' }}>{{ $item['name'] }}</option>
                            @endforeach
                        </select>
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
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter Log</button>
                        <a href="{{ route('admin.reports.stock-detail-report') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0">Chronological Movement Log for: <span class="text-primary">{{ $selectedName }}</span></h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Type / Direction</th>
                        <th>Reference / Document</th>
                        <th class="text-center">Quantity</th>
                        <th>Notes / Party Info</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movementLogs as $log)
                        <tr>
                            <td>{{ $log['date'] }}</td>
                            <td>
                                @if(str_contains($log['type'], 'IN'))
                                    <span class="badge bg-success"><i class="bx bx-down-arrow-alt"></i> {{ $log['type'] }}</span>
                                @else
                                    <span class="badge bg-danger"><i class="bx bx-up-arrow-alt"></i> {{ $log['type'] }}</span>
                                @endif
                            </td>
                            <td><code>{{ $log['ref_no'] }}</code></td>
                            <td class="text-center fw-bold">{{ $log['qty'] }}</td>
                            <td>{{ $log['party'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No stock movement logged for this item during the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
