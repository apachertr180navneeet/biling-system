@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-file"></i></div>
            <div>
                <h4 class="m-page-title">AMC: {{ $amc->contract_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.maintenance.amcs.index') }}">AMC</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $amc->contract_number }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.maintenance.amcs.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Contract Details</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Contract Number</label>
                            <p class="mb-0 fw-semibold">{{ $amc->contract_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Asset</label>
                            <p class="mb-0 fw-semibold">{{ $amc->asset?->name ?? '-' }} ({{ $amc->asset?->asset_code ?? '-' }})</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Vendor</label>
                            <p class="mb-0">{{ $amc->vendor?->company_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Status</label>
                            <p class="mb-0">
                                @php $sc = ['active'=>'success','expired'=>'warning','cancelled'=>'danger']; @endphp
                                <span class="badge bg-label-{{ $sc[$amc->status] ?? 'secondary' }}">{{ ucfirst($amc->status) }}</span>
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">Start Date</label>
                            <p class="mb-0">{{ $amc->start_date->format('d-m-Y') }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">End Date</label>
                            <p class="mb-0">{{ $amc->end_date->format('d-m-Y') }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">Cost</label>
                            <p class="mb-0 fw-semibold">₹{{ number_format($amc->cost, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Contact</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted mb-0">Contact Person</label>
                        <p class="mb-0">{{ $amc->contact_person ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted mb-0">Contact Phone</label>
                        <p class="mb-0">{{ $amc->contact_phone ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Description</h5></div>
                <div class="card-body">
                    <p class="mb-0">{{ $amc->description ?? 'No description provided.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
