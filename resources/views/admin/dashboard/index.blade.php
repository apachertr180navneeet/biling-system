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
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><span class="d-block mb-1 text-muted">Customers</span><h3 class="mb-0">{{ $totalCustomers }}</h3></div>
                        <span class="badge bg-label-info p-2 rounded"><i class="bx bx-user stat-icon"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mb-4">
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
        <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><span class="d-block mb-1 text-muted">Part Inventory</span><h3 class="mb-0">{{ $lowStockCount }}</h3></div>
                        <span class="badge bg-label-warning p-2 rounded"><i class="bx bx-box stat-icon"></i></span>
                    </div>
                    <small class="text-muted">low stock items</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
