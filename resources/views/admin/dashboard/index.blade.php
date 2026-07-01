@extends('admin.layouts.app')
@section('style')
<style>
.stat-icon { font-size: 1.5rem; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Dashboard
    </h4>
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-4 p-md-5 text-center">
                    <h2 class="text-primary mb-2">Welcome, {{ Auth::user()->full_name }}</h2>
                    <p class="mb-0 text-muted">Today's invoices: <strong>{{ $todayInvoices }}</strong> | Month revenue: <strong>{{ number_format($monthRevenue, 2) }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><span class="d-block mb-1 text-muted">Total Revenue</span><h3 class="mb-0">{{ number_format($totalRevenue, 2) }}</h3></div>
                        <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-wallet stat-icon"></i></span>
                    </div>
                    <small class="text-muted">{{ $totalInvoices }} invoices</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><span class="d-block mb-1 text-muted">Customers</span><h3 class="mb-0">{{ $totalCustomers }}</h3></div>
                        <span class="badge bg-label-info p-2 rounded"><i class="bx bx-user stat-icon"></i></span>
                    </div>
                    <small class="text-muted">{{ $pendingInvoices }} pending invoices</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><span class="d-block mb-1 text-muted">Total Invoices</span><h3 class="mb-0">{{ $totalInvoices }}</h3></div>
                        <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-receipt stat-icon"></i></span>
                    </div>
                    <small class="text-muted">{{ number_format($totalRevenue, 2) }} revenue</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><span class="d-block mb-1 text-muted">Vehicle Stock</span><h3 class="mb-0">{{ $vehicleInventoryCount }}</h3></div>
                        <span class="badge bg-label-success p-2 rounded"><i class="bx bx-car stat-icon"></i></span>
                    </div>
                    <small class="text-muted">{{ $pendingVehiclePOs }} pending POs</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Recent Invoices</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>#</th><th>Invoice</th><th>Customer</th><th>Amount</th></tr></thead>
                        <tbody>
                            @forelse($recentInvoices as $inv)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $inv->invoice_number }}</td><td>{{ $inv->customer->first_name ?? '' }} {{ $inv->customer->last_name ?? '' }}</td><td>{{ number_format($inv->grand_total, 2) }}</td></tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted">No invoices yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Recent Payments</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>#</th><th>Payment</th><th>Customer</th><th>Amount</th></tr></thead>
                        <tbody>
                            @forelse($recentPayments as $pay)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $pay->payment_number }}</td><td>{{ $pay->customer->first_name ?? '' }} {{ $pay->customer->last_name ?? '' }}</td><td>{{ number_format($pay->amount, 2) }}</td></tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted">No payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
