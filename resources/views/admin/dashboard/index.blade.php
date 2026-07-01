@extends('admin.layouts.app')
@section('style')
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
                        <h2 class="text-primary mb-2">Welcome, {{ Auth::user()->full_name }} 🚀</h2>
                        <p class="mb-0 text-muted">Manage your vehicle billing system with style and efficiency.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Stats Cards -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-3">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-primary p-2 rounded"><i class="bx bx-car fs-3"></i></span>
                            </div>
                        </div>
                        <span class="d-block mb-1 text-muted">Total Vehicles</span>
                        <h3 class="card-title text-nowrap mb-2">1,245</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-3">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-success p-2 rounded"><i class="bx bx-wallet fs-3"></i></span>
                            </div>
                        </div>
                        <span class="d-block mb-1 text-muted">Total Revenue</span>
                        <h3 class="card-title text-nowrap mb-2">$34,245</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +12.4%</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-3">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-info p-2 rounded"><i class="bx bx-user fs-3"></i></span>
                            </div>
                        </div>
                        <span class="d-block mb-1 text-muted">Active Users</span>
                        <h3 class="card-title text-nowrap mb-2">342</h3>
                        <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> -3.2%</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-3">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-warning p-2 rounded"><i class="bx bx-file fs-3"></i></span>
                            </div>
                        </div>
                        <span class="d-block mb-1 text-muted">Pending Invoices</span>
                        <h3 class="card-title text-nowrap mb-2">45</h3>
                        <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +1.5%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
