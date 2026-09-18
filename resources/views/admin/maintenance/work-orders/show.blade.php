@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-task"></i></div>
            <div>
                <h4 class="m-page-title">Work Order: {{ $workOrder->work_order_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.maintenance.work-orders.index') }}">Work Orders</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ $workOrder->work_order_number }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.maintenance.work-orders.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Work Order Details</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">WO Number</label>
                            <p class="mb-0 fw-semibold">{{ $workOrder->work_order_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Title</label>
                            <p class="mb-0 fw-semibold">{{ $workOrder->title }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Type</label>
                            <p class="mb-0">{{ ucfirst($workOrder->type) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Priority</label>
                            <p class="mb-0">
                                @php $pc = ['low'=>'info','medium'=>'warning','high'=>'danger']; @endphp
                                <span class="badge bg-label-{{ $pc[$workOrder->priority] ?? 'secondary' }}">{{ ucfirst($workOrder->priority) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Asset</label>
                            <p class="mb-0">{{ $workOrder->asset ? $workOrder->asset->asset_code . ' - ' . $workOrder->asset->name : '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Assigned To</label>
                            <p class="mb-0">{{ $workOrder->assignedEmployee ? $workOrder->assignedEmployee->first_name . ' ' . $workOrder->assignedEmployee->last_name : '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted mb-0">Status</label>
                            <p class="mb-0">
                                @php $sc = ['pending'=>'warning','in_progress'=>'info','completed'=>'success','cancelled'=>'danger']; @endphp
                                <span class="badge bg-label-{{ $sc[$workOrder->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$workOrder->status)) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Schedule & Cost</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">Schedule Date</label>
                            <p class="mb-0">{{ $workOrder->schedule_date?->format('d-m-Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">Completion Date</label>
                            <p class="mb-0">{{ $workOrder->completion_date?->format('d-m-Y') ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted mb-0">Cost</label>
                            <p class="mb-0 fw-semibold">₹{{ number_format($workOrder->cost, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Description</h5></div>
                <div class="card-body">
                    <p class="mb-0">{{ $workOrder->description ?? 'No description provided.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
