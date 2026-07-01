@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Customer Ledger</h4>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card"><div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Select Customer</label>
                <select name="customer_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ ($selectedCustomer && $selectedCustomer->id == $c->id) ? 'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->mobile }})</option>
                    @endforeach
                </select>
            </div>
            @if($selectedCustomer)
            <div class="col-md-6 d-flex align-items-end">
                <a href="?customer_id={{ $selectedCustomer->id }}" class="btn btn-primary">Refresh</a>
            </div>
            @endif
        </form>
    </div></div>

    @if($selectedCustomer)
    <div class="card mt-3"><div class="card-body">
        <h5>{{ $selectedCustomer->first_name }} {{ $selectedCustomer->last_name }}</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Date</th><th>Type</th><th>No</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td>{{ $t['date'] instanceof \Carbon\Carbon ? $t['date']->format('d-m-Y') : $t['date'] }}</td>
                        <td><span class="badge bg-{{ $t['type']=='Invoice' ? 'primary' : 'success' }}">{{ $t['type'] }}</span></td>
                        <td>{{ $t['no'] }}</td>
                        <td>{{ $t['debit'] > 0 ? number_format($t['debit'], 2) : '-' }}</td>
                        <td>{{ $t['credit'] > 0 ? number_format($t['credit'], 2) : '-' }}</td>
                        <td><strong>{{ number_format($t['balance'], 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No transactions.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>
    @endif
</div>
@endsection
