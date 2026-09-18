@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-wrench"></i></div>
            <div>
                <h4 class="m-page-title">Asset: {{ $asset->name }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.maintenance.assets.index') }}">Assets</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $asset->asset_code }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.maintenance.assets.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Asset Details</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Asset Code</label>
                            <p class="mb-0 fw-semibold">{{ $asset->asset_code }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Name</label>
                            <p class="mb-0 fw-semibold">{{ $asset->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Category</label>
                            <p class="mb-0">{{ $asset->category }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Location</label>
                            <p class="mb-0">{{ $asset->location ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Brand</label>
                            <p class="mb-0">{{ $asset->brand ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Model</label>
                            <p class="mb-0">{{ $asset->model ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Serial Number</label>
                            <p class="mb-0">{{ $asset->serial_number ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Status</label>
                            <p class="mb-0">
                                @php
                                    $sc = ['active'=>'success','inactive'=>'secondary','under_maintenance'=>'warning','disposed'=>'danger'];
                                @endphp
                                <span class="badge bg-label-{{ $sc[$asset->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$asset->status)) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Purchase & Warranty</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">Purchase Date</label>
                            <p class="mb-0">{{ $asset->purchase_date?->format('d-m-Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">Purchase Cost</label>
                            <p class="mb-0">₹{{ $asset->purchase_cost ? number_format($asset->purchase_cost, 2) : '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">Warranty Expiry</label>
                            <p class="mb-0">{{ $asset->warranty_expiry_date?->format('d-m-Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Description</h5></div>
                <div class="card-body">
                    <p class="mb-0">{{ $asset->description ?? 'No description provided.' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($asset->amcs->count() > 0)
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">AMC Contracts</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Contract #</th><th>Vendor</th><th>Start</th><th>End</th><th>Cost</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($asset->amcs as $amc)
                        <tr>
                            <td>{{ $amc->contract_number }}</td>
                            <td>{{ $amc->vendor?->company_name ?? '-' }}</td>
                            <td>{{ $amc->start_date->format('d-m-Y') }}</td>
                            <td>{{ $amc->end_date->format('d-m-Y') }}</td>
                            <td>₹{{ number_format($amc->cost, 2) }}</td>
                            <td>
                                @php $amcSc = ['active'=>'success','expired'=>'warning','cancelled'=>'danger']; @endphp
                                <span class="badge bg-label-{{ $amcSc[$amc->status] ?? 'secondary' }}">{{ ucfirst($amc->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($asset->workOrders->count() > 0)
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Work Orders</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>WO #</th><th>Title</th><th>Type</th><th>Priority</th><th>Assigned To</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($asset->workOrders as $wo)
                        <tr>
                            <td>{{ $wo->work_order_number }}</td>
                            <td>{{ $wo->title }}</td>
                            <td>{{ ucfirst($wo->type) }}</td>
                            <td>
                                @php $pc = ['low'=>'info','medium'=>'warning','high'=>'danger']; @endphp
                                <span class="badge bg-label-{{ $pc[$wo->priority] ?? 'secondary' }}">{{ ucfirst($wo->priority) }}</span>
                            </td>
                            <td>{{ $wo->assignedEmployee ? $wo->assignedEmployee->first_name . ' ' . $wo->assignedEmployee->last_name : '-' }}</td>
                            <td>
                                @php $wsc = ['pending'=>'warning','in_progress'=>'info','completed'=>'success','cancelled'=>'danger']; @endphp
                                <span class="badge bg-label-{{ $wsc[$wo->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$wo->status)) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
